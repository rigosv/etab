<?php

namespace App\AlmacenamientoDatos\Driver;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\AlmacenamientoDatos\OrigenDatosInterface;
use App\Service\Util;

class CouchbaseOrigenDatos implements OrigenDatosInterface
{
    private $bucket;
    private $bucketAux;
    private $doc = 'origen_';
    private $bucketName = 'etab_origenes';
    private $bucketAuxName = 'etab_origenes_aux';

    public function __construct( ParameterBagInterface $params)
    {

        $authenticator = new \Couchbase\PasswordAuthenticator();

        $authenticator->username($params->get('couchbase_user'))->password($params->get('couchbase_password'));

        // Connect to Couchbase Server
        $cluster = new \CouchbaseCluster($params->get('couchbase_url'));

        // Authenticate, then open bucket
        $cluster->authenticate($authenticator);
        $this->bucket = $cluster->openBucket($this->bucketName);
        $this->bucketAux = $cluster->openBucket($this->bucketAuxName);

        $this->bucket->operationTimeout = 240 * 100; // 240 segundos
        $this->bucketAux->operationTimeout = 240 * 100; // 240 segundos

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

            $datos_a_enviar[] = $nueva_fila;
        }

        return $datos_a_enviar;
    }

    public function inicializarTablaAuxliar($idOrigenDatos, $idConexion) {
        $this->borrarTablaAuxiliar($idOrigenDatos, $idConexion);
    }

    public function insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos ) {

        $parte = uniqid();
        $docName = $this->doc . $idOrigenDatos .'_cnx_'.$idConexion. '_parte_'. $parte ;

        $filas = ['id_origen_datos'=>(integer)$idOrigenDatos,
            'id_conexion' => (integer) $idConexion,
            'datos'  => $datos
        ];
        $this->bucketAux->upsert($docName, $filas);

    }

    public function borrarTablaAuxiliar($idOrigenDatos, $idConexion) {
        $this->borrarDocumento($this->bucketAuxName, $idOrigenDatos, $idConexion);
    }

    public function inicializarTabla($idOrigenDatos, $idConexion) {
        //No es necesaria está función con couchbase
    }


    public function guardarDatos($idConexion, $idOrigenDatos) {

        //Borrar los datos existentes
        $this->borrarDocumento($this->bucketName, $idOrigenDatos, $idConexion);

        $stm = 'UPSERT INTO `' . $this->bucketName . '` (KEY _k, VALUE _v) ' .
            ' SELECT META().id _k, _v ' .
            ' FROM `' . $this->bucketAuxName . '` _v' .
            ' WHERE id_origen_datos = ' . $idOrigenDatos . ' AND id_conexion = ' . $idConexion;
        $query = \Couchbase\N1qlQuery::fromString($stm);
        $this->bucket->query($query);

        //Borrar los documentos temporales
        $this->borrarDocumento($this->bucketAuxName, $idOrigenDatos, $idConexion);
    }

    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $limiteInf, $limiteSup){

    }

    private function borrarDocumento($bucketNane, $idOrigenDatos, $idConexion) {
        try {
            $stm = 'DELETE FROM `'.$bucketNane.'` WHERE id_origen_datos= ' . $idOrigenDatos . ' AND id_conexion = ' . $idConexion;
            $query = \Couchbase\N1qlQuery::fromString($stm);
            $this->bucketAux->query($query);
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }
}