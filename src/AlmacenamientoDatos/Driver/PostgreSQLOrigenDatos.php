<?php

namespace App\AlmacenamientoDatos\Driver;

use Doctrine\ORM\EntityManager;

use App\Entity\OrigenDatos;
use App\AlmacenamientoDatos\OrigenDatosInterface;
use App\Service\Util;

class PostgreSQLOrigenDatos implements OrigenDatosInterface
{
    private $em;
    private $cnx;
    private $pdo;
    private $tabla = 'origenes.fila_origen_dato_';
    private $emDatos;

    public function __construct(EntityManager $em, EntityManager $emDatos)
    {
        $this->em = $em;
        $this->cnx = $emDatos->getConnection();
        $this->emDatos = $emDatos;
        $this->pdo = $this->cnx->getWrappedConnection();

    }

    public function prepararDatosEnvio($idOrigenDatos, $campos_sig, $datos, $ultimaLectura, $idConexion){
        $util = new Util();
        //Cambiar el nombre de las llaves, en lugar de usar el nombre del campo
        // se usará el nombre del significado
        $datos_a_enviar = array();

        foreach ($datos as $fila) {
            $nueva_fila = array();
            foreach ($fila as $k => $v) {
                $v_ = preg_replace("/[\r\n|\n|\r]+/", " ", $v);
                $nueva_fila[$campos_sig[$util->slug($k)]] = trim(mb_check_encoding($v_, 'UTF-8') ? $v_ : mb_convert_encoding($v_, 'UTF-8'));
            }
            $filaJson = json_encode($nueva_fila, JSON_UNESCAPED_UNICODE);
            $datos_a_enviar[] = "$idOrigenDatos\t". str_replace('"', '\"' , $filaJson )."\t$ultimaLectura\t$idConexion";
        }

        return $datos_a_enviar;
    }

    public function inicializarTablaAuxliar($idOrigenDatos, $idConexion) {
        $sql = ' CREATE TABLE IF NOT EXISTS  '. $this->tabla.$idOrigenDatos.'_tmp (
                    id_origen_dato integer,
                    datos jsonb,
                    ultima_lectura timestamp,
                    id_conexion integer
                )' ;

        $this->cnx->exec($sql);
    }

    public function insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos, $idCarga) {
        $this->pdo->pgsqlCopyFromArray($this->tabla.$idOrigenDatos.'_tmp', $datos);
    }

    public function borrarTablaAuxiliar($idOrigenDatos, $idConexion) {
        $sql = ' DROP TABLE IF EXISTS '.$this->tabla.$idOrigenDatos.'_tmp ';
        $this->cnx->exec($sql);
    }

    public function inicializarTabla($idOrigenDatos, $idConexion) {

        $nombreTabla = $this->tabla.$idOrigenDatos;

        try {
            $this->cnx->query("select * from $nombreTabla LIMIT 1");
        } catch (\Exception $e) {
            //Crear la tabla
            $this->cnx->exec("select * INTO $nombreTabla from $nombreTabla"."_tmp LIMIT 0 ");
        }

    }

    public function guardarDatos($idConexion, $idOrigenDatos, $idCarga) {

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

        $this->borrarTablaAuxiliar($idOrigenDatos, $idOrigenDatos);
    }

    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $idCarga, $limiteInf, $limiteSup){

        $origenDato = $this->em->find(OrigenDatos::class, $idOrigenDatos);
        $campoControlIncremento = $origenDato->getCampoLecturaIncremental()->getSignificado()->getCodigo();

        $formatoFecha = str_replace(['Y', 'm', 'd', 'H', 'i', 's'], ['YYYY', 'MM', 'DD', 'HH', 'MI', 'SS'], $origenDato->getFormatoValorCorte() );

        $tablaDestino = $this->tabla.$idOrigenDatos;
        $tablaAuxiliar = $tablaDestino.'_tmp';
        $sql = "DELETE 
                        FROM $tablaDestino 
                        WHERE to_date( datos->'$campoControlIncremento', $formatoFecha) >= to_date( '$limiteInf', $formatoFecha )
                            AND to_date( datos->'$campoControlIncremento', $formatoFecha ) <= to_date( '$limiteSup', $formatoFecha )
                            AND ( (id_origen_dato = $idOrigenDatos and id_conexion = $idConexion)
                              OR (id_origen_dato = $idOrigenDatos AND id_conexion is null)
                              )
                            ;
                            
                INSERT INTO $tablaDestino SELECT * FROM $tablaAuxiliar 
                  WHERE id_origen_dato = $idOrigenDatos and id_conexion = $idConexion;
                  
                DROP TABLE IF EXISTS $tablaAuxiliar ;";

        $this->cnx->exec($sql);
    }

}