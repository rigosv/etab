<?php

namespace App\AlmacenamientoDatos\Driver;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\OrigenDatos;
use App\AlmacenamientoDatos\OrigenDatosInterface;
use App\Service\Util;

class CouchbaseOrigenDatos implements OrigenDatosInterface
{
    private $bucket;
    private $doc = 'origen_';
    private $bucketName = 'etab_origenes';

    public function __construct( ParameterBagInterface $params)
    {

        $authenticator = new \Couchbase\PasswordAuthenticator();
        $authenticator->username($params->get('couchbase_user'))->password($params->get('couchbase_password'));

        // Connect to Couchbase Server
        $cluster = new \CouchbaseCluster($params->get('couchbase_url'));

        // Authenticate, then open bucket
        $cluster->authenticate($authenticator);
        $this->bucket = $cluster->openBucket($this->bucketName);

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

    public function insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos) {
        $docName = $this->doc . $idOrigenDatos .'_cnx_'.$idConexion. '_tmp';

        if ( !$this->existeDocumento($docName) ) {
            // Crear el documento
            $filas = ['id_origen_datos'=>(integer)$idOrigenDatos, 'datos'  => $datos];
            $this->bucket->insert($docName, $filas);
        } else {
            //ya existe, actualizarlo
            $datosJson = trim(trim(json_encode($datos),'[') ,']');
            $stm = 'UPDATE `'.$this->bucketName.'` USE KEYS "'.$docName.'" SET datos = ARRAY_PUT(IFNULL(datos, []), '.$datosJson.')' ;

            $query = \Couchbase\N1qlQuery::fromString($stm);
            $this->bucket->query($query);
        }

    }

    public function borrarTablaAuxiliar($idOrigenDatos, $idConexion) {
        $this->borrarDocumento($this->doc . $idOrigenDatos.'_cnx_'.$idConexion . '_tmp');
    }

    public function inicializarTabla($idOrigenDatos, $idConexion) {
        //No es necesaria está función con couchbase
    }


    public function guardarDatos($idConexion, $idOrigenDatos) {

        $docName = $this->doc . $idOrigenDatos . '_cnx_'.$idConexion;
        $docAux = $docName. '_tmp';

        $r = $this->bucket->get($docAux);
        $this->bucket->upsert($docName, $r->value);

        //Borrar la tabla temporal
        $this->borrarDocumento($docAux);
    }

    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $campoControlIncremento, $limiteInf, $limiteSup){


    }


    private function existeDocumento($docName){
        $existe = true;
        try {
            $this->bucket->get( $docName );
        } catch (\Exception $e){
            $existe = false;
        }
        return $existe;
    }

    private function borrarDocumento($docName) {
        try {
            $this->bucket->remove($docName);
        }catch (\Exception $e){}
    }
}