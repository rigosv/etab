<?php

namespace App\AlmacenamientoDatos;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\FichaTecnica;
use App\Entity\OrigenDatos;
use App\AlmacenamientoDatos\OrigenDatosInterface;
use App\AlmacenamientoDatos\DashboardInterface;
use App\AlmacenamientoDatos\Driver\PostgreSQLDashboard;
use App\AlmacenamientoDatos\Driver\PostgreSQLOrigenDatos;
use App\AlmacenamientoDatos\Driver\CouchbaseOrigenDatos;
use App\AlmacenamientoDatos\Driver\CouchbaseDashboard;
use App\Entity\SignificadoCampo;




class AlmacenamientoProxy implements DashboardInterface, OrigenDatosInterface
{
    private $params;
    private $em;
    private $dashboardWrapped;
    private $origenDatosWrapped;
    private $emDatos;

    public function __construct(ContainerInterface $container, ParameterBagInterface $params)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->emDatos = $container->get('doctrine.orm.etab_datos_entity_manager');
        $this->params = $params;

        //Por defecto es PostgresSQL
        if ($this->params->get('app.datos.tipo_almacenamiento') == 'couchbase'){
            $this->dashboardWrapped = new CouchbaseDashboard($this->em, $this->params);
            $this->origenDatosWrapped = new CouchbaseOrigenDatos($this->em, $this->params);
        } else {
            $this->dashboardWrapped = new PostgreSQLDashboard($this->em, $this->emDatos);
            $this->origenDatosWrapped = new PostgreSQLOrigenDatos($this->em, $this->emDatos);
        }

    }

    // **** MÉTODOS PARA LOS ORÍGENES DE DATOS

    public function prepararDatosEnvio($idOrigenDatos, $campos_sig, $datos, $ultimaLectura, $idConexion){
        return $this->origenDatosWrapped->prepararDatosEnvio($idOrigenDatos, $campos_sig, $datos, $ultimaLectura, $idConexion);
    }

    public function inicializarTablaAuxliar($idOrigenDatos ){
        $this->origenDatosWrapped->inicializarTablaAuxliar($idOrigenDatos);
    }

    public function insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos, $idCarga){
        $this->origenDatosWrapped->insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos, $idCarga);
    }


    public function borrarTablaAuxiliar($idOrigenDatos, $idConexion ) {
        $this->origenDatosWrapped->borrarTablaAuxiliar($idOrigenDatos, $idConexion);
    }


    public function inicializarTabla($idOrigenDatos, $idConexion){
        $this->origenDatosWrapped->inicializarTabla($idOrigenDatos, $idConexion);
    }


    public function guardarDatos($idConexion, $idOrigenDatos, $idCarga) {
        $this->origenDatosWrapped->guardarDatos($idConexion, $idOrigenDatos, $idCarga);
    }


    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $idCarga, $limiteInf, $limiteSup){
        $this->origenDatosWrapped->guardarDatosIncremental($idConexion, $idOrigenDatos, $idCarga, $limiteInf, $limiteSup);
    }


    // ********* MÉTODOS DEL TABLERO

    public function crearIndicador(FichaTecnica $fichaTec, $dimension = null, $filtros = null) {
        $this->dashboardWrapped->crearIndicador($fichaTec, $dimension, $filtros);
    }


    public function calcularIndicador($fichaTec, $dimension, $filtros, $verSql, $filtro_adicional = '', $tendencia = false, $dimensionComparacion = null){

        //Verificar si en los filtros vienen datos que son catálogos
        if ($filtros != null) {
            $newFiltros = [];
            foreach ($filtros as $campo => $valor) {
                //Si el filtro es un catálogo, buscar su id correspondiente
                $significado = $this->em->getRepository(SignificadoCampo::class)
                    ->findOneBy(array('codigo' => $campo));
                $catalogo = $significado->getCatalogo();

                if ($catalogo != '') {
                    $sql_ctl = "SELECT id FROM $catalogo WHERE descripcion ='$valor'";
                    $reg = $this->em->getConnection()->executeQuery($sql_ctl)->fetch();
                    $valor = $reg['id'];
                }
                $newFiltros[$campo] = $valor;
            }
            $filtros = $newFiltros;
        }

        $datos = $this->dashboardWrapped->calcularIndicador($fichaTec, $dimension, $filtros, $verSql, $filtro_adicional, $tendencia, $dimensionComparacion);        
        
        foreach (['category' => $dimension, 'subcategory' => $dimensionComparacion] as $tipo=>$dim) {
            
            //Verificar si la dimensión es un catálogo
            $significado = $this->em->getRepository(SignificadoCampo::class)
                ->findOneBy(['codigo' => $dim]);
            $catalogo = ($significado === null) ? '' : $significado->getCatalogo();
            
            if ($catalogo != '' ) {            
                if ( $verSql ) {
                    $datos = preg_replace('/\b' . $dim . '\b/', 'B.descripcion', $datos);
                    $datos = preg_replace('/\bA\b/', 'A INNER JOIN '. $catalogo . ' B ON (A. '.$dim.' = B.id) ', $datos);
                } else {
                    //Las coincidencias a buscar
                    $buscar = [];            
                    foreach ($datos as $d ){ $buscar[] = $d[$tipo]; }

                    $sql_ctl = "SELECT id, descripcion FROM $catalogo WHERE id IN (".implode(',', $buscar).")";
                    try {
                        $datCatalogo = $this->em->getConnection()->executeQuery($sql_ctl)->fetchAll();
                        $datosSust = [];                
                        foreach ($datCatalogo as $dc ){ $datosSust[$dc['id']] = $dc['descripcion'] ;}

                        //Hacer la sustitución, en lugar de mandar los ids de los catálogos, mandar la descripción
                        $newDatos = [];
                        foreach ($datos as $d ){
                            if ( array_key_exists($d[$tipo], $datosSust) ) {
                                $d['id'] = $d[$tipo];
                                $d[$tipo] = $datosSust[$d[$tipo]];

                            }
                            $newDatos[] = $d;
                        }
                        $datos = $newDatos;
                    } catch ( \Exception $e ) {

                    }
                }
            }
        }        

        return $datos;
    }


    public function getAnalisisDescriptivo($sql){
        return  $this->dashboardWrapped->getAnalisisDescriptivo($sql);
    }


    public function totalRegistrosIndicador(FichaTecnica $fichaTec){
        return $this->dashboardWrapped->totalRegistrosIndicador($fichaTec);
    }

    public function getDatosIndicador(FichaTecnica $fichaTecnica, $offset = 0 , $limit = 100000000) {
        $datos = $this->dashboardWrapped->getDatosIndicador($fichaTecnica, $offset, $limit);

        $campos_indicador = explode(',', str_replace(' ', '', $fichaTecnica->getCamposIndicador()));
        $datosSust = [];
        foreach ($campos_indicador as $c) {
            $significado = $this->em->getRepository(SignificadoCampo::class)
                ->findOneBy(array('codigo' => $c));

            $catalogo = $significado->getCatalogo();
            if ($catalogo != '') {
                $sql_ctl = "SELECT id, descripcion FROM $catalogo ";
                try {
                    $datCatalogo = $this->em->getConnection()->executeQuery($sql_ctl)->fetchAll();
                    foreach ($datCatalogo as $dc) {
                        $datosSust[$c][$dc['id']] = $dc['descripcion'];
                    }
                } catch (\Exception $e){}
            }
        }

        //Sustituir los datos de los catálogos
        if ( count( $datosSust ) > 0 ) {
            $datosNew = [];
            
            foreach ( $datos as $fila ) {
                foreach( $datosSust as $campo => $valor ){
                    if (array_key_exists($fila[$campo], $datosSust[$campo]) and array_key_exists($campo, $datosSust)) {
                        $fila[ $campo.'_' ] = $fila[$campo];
                        $fila[ $campo ] = $datosSust[$campo][$fila[$campo]];
                    }
                }
                $datosNew[] = $fila;
            }
            $datos = $datosNew;
        }

        return $datos;
    }

}