<?php

namespace App\AlmacenamientoDatos\Driver;

use Doctrine\ORM\EntityManager;

use App\AlmacenamientoDatos\AbstractDriver;
use App\Entity\OrigenDatos;

class PostgreSQLOrigenDatos implements OrigenDatosInterface
{
    private $em;
    private $cnx;
    private $pdo;
    private $tabla = 'origenes.fila_origen_dato_';


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $cnx = $em->getConnection('etab-datos');
        $this->pdo = $cnx->getWrappedConnection();
    }

    public function inicializarTablaAuxliar($idOrigenDatos) {
        $sql = ' DROP TABLE IF EXISTS '.$this->tabla.$idOrigenDatos.'_tmp;
                SELECT * INTO '.$this->tabla.$idOrigenDatos."_tmp FROM fila_origen_dato_v2 LIMIT 0;                
               ";
        $this->cnx->exec($sql);
    }

    public function insertarEnAuxiliar($tabla, $datos) {
        $this->pdo->pgsqlCopyFromArray($tabla, $datos);
    }

    public function borrarTablaAuxiliar($tabla) {
        $sql = ' DROP TABLE IF EXISTS '.$tabla.'_tmp ';
        $this->cnx->exec($sql);
    }

    public function inicializarTabla($nombreTabla) {

        try {
            $this->cnx->query("select * from $nombreTabla LIMIT 1");
        } catch (\Doctrine\DBAL\DBALException $e) {
            //Crear la tabla
            $this->cnx->exec("select * INTO $nombreTabla from $nombreTabla"."_tmp LIMIT 0 ");
        }

    }

    public function guardarDatos($idConexion, $idOrigenDatos) {

        $nuevosDatos = $this->pdo->pgsqlCopyToArray($this->tabla.$idOrigenDatos.'_tmp');

        if ( count($nuevosDatos) > 0 ){
            //Borrar los datos anteriores
            $sql = "DELETE 
                        FROM " .$this->tabla .$idOrigenDatos. "  
                        WHERE (id_origen_dato = $idOrigenDatos and id_conexion = $idConexion) 
                            OR (id_origen_dato = $idOrigenDatos AND id_conexion is null) ";
            $this->cnx->exec($sql);

            $this->pdo->pgsqlCopyFromArray($this->tabla.$idOrigenDatos, $nuevosDatos);
        }

        $this->borrarTablaAuxiliar($this->tabla.$idOrigenDatos);
    }

    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $campoControlIncremento, $limiteInf, $limiteSup){

        $tablaDestino = $this->tabla.$idOrigenDatos;
        $tablaAuxiliar = $tablaDestino.'_tmp';
        $sql = "DELETE 
                        FROM $tablaDestino 
                        WHERE datos->'$campoControlIncremento' >= '$limiteInf'
                            AND datos->'$campoControlIncremento' <= '$limiteSup'
                            AND ( (id_origen_dato = $idOrigenDatos and id_conexion = $idConexion)
                              OR (id_origen_dato = $idOrigenDatos AND id_conexion is null)
                              )
                            ;
                            
                INSERT INTO $tablaDestino SELECT * FROM $tablaAuxiliar 
                  WHERE id_origen_dato = $idOrigenDatos and id_conexion = $idConexion;
                  
                DROP TABLE IF EXISTS $tablaAuxiliar ;";

        $this->cnx->exec($sql);
    }

    public function cargarCatalogo(OrigenDatos $origenDato)
    {
        $origenDatosRepository =  $this->em->getRepository(OrigenDatos::class);

        $datos = array();
        if (count($origenDato->getConexiones()) > 0) {
            foreach ($origenDato->getConexiones() as $cnx) {
                $datos_cnx = $origenDatosRepository->getDatos($origenDato->getSentenciaSql(), $cnx);
                $datos = array_merge($datos, $datos_cnx);
            }
        } else {
            $datos = $origenDatosRepository->getDatos(null, null, $origenDato->getAbsolutePath());
        }

        return $origenDatosRepository->crearTablaCatalogo($origenDato, $datos);
    }
}