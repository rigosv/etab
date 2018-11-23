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
            //$this->dashboardWrapped = new CouchbaseDashboard($this->em);
            $this->origenDatosWrapped = new CouchbaseOrigenDatos($this->params);
        } else {
            $this->dashboardWrapped = new PostgreSQLDashboard($this->em, $this->emDatos);
            $this->origenDatosWrapped = new PostgreSQLOrigenDatos($this->em, $this->emDatos);
        }

    }

    // **** MÉTODOS PARA LOS ORÍGENES DE DATOS

    public function prepararDatosEnvio($idOrigenDatos, $campos_sig, $datos, $ultimaLectura, $idConexion){
        return $this->origenDatosWrapped->prepararDatosEnvio($idOrigenDatos, $campos_sig, $datos, $ultimaLectura, $idConexion);
    }

    public function inicializarTablaAuxliar($idOrigenDatos, $idConexion ){
        $this->origenDatosWrapped->inicializarTablaAuxliar($idOrigenDatos, $idConexion);
    }

    public function insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos){
        $this->origenDatosWrapped->insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos);
    }


    public function borrarTablaAuxiliar($idOrigenDatos, $idConexion ) {
        $this->origenDatosWrapped->borrarTablaAuxiliar($idOrigenDatos, $idConexion);
    }


    public function inicializarTabla($idOrigenDatos, $idConexion){
        $this->origenDatosWrapped->inicializarTabla($idOrigenDatos, $idConexion);
    }


    public function guardarDatos($idConexion, $idOrigenDatos) {
        $this->origenDatosWrapped->guardarDatos($idConexion, $idOrigenDatos);
    }


    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $campoControlIncremento, $limiteInf, $limiteSup){
        $this->origenDatosWrapped->guardarDatosIncremental($idConexion, $idOrigenDatos, $campoControlIncremento, $limiteInf, $limiteSup);
    }

    public function cargarCatalogo(OrigenDatos $origenDato){
        $this->origenDatosWrapped->cargarCatalogo($origenDato);
    }


    // ********* MÉTODOS DEL TABLERO

    public function crearIndicador(FichaTecnica $fichaTec, $dimension, $filtros) {
        $this->dashboardWrapped->crearIndicador($fichaTec, $dimension, $filtros);
    }


    public function calcularIndicador($fichaTec, $dimension, $filtros, $verSql){
        return $this->dashboardWrapped->calcularIndicador($fichaTec, $dimension, $filtros, $verSql);
    }


    public function getAnalisisDescriptivo($sql){
       return  $this->dashboardWrapped->getAnalisisDescriptivo($sql);
    }


    public function totalRegistrosIndicador(FichaTecnica $fichaTec){
        return $this->dashboardWrapped->totalRegistrosIndicador($fichaTec);
    }

    public function crearCamposIndicador (FichaTecnica $fichaTec){
        $this->dashboardWrapped->crearCamposIndicador($fichaTec);
    }
}