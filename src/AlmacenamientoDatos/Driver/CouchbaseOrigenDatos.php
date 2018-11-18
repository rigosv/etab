<?php

namespace App\AlmacenamientoDatos\Driver;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\OrigenDatos;
use App\AlmacenamientoDatos\OrigenDatosInterface;
use App\Service\Util;

class CouchbaseOrigenDatos implements OrigenDatosInterface
{
    private $bucket;
    private $doc = 'origenes.fila_origen_dato_';
    private $bucketName = 'etab_datos';

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

    public function inicializarTablaAuxliar($idOrigenDatos) {

        try {
            $this->bucket->remove($this->doc . $idOrigenDatos . '_tmp');
        } catch (\Exception $e){}
    }

    public function insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos) {
        $docName = $this->doc . $idOrigenDatos . '_tmp';

        try{
            // Insertar, si ya existe el documento dará una excepción y se pasará a actualizar
            $filas = ['id_origen_dato' => $idOrigenDatos, 'conexiones' => ['id_conexion' => $idConexion, 'datos' => $datos]];
            $this->bucket->insert($docName, $filas);
        } catch (\Exception $e){
            //ya existe, actualizarlo
            $datosJson = trim(trim(json_encode($datos),'[') ,']');
            $stm = 'UPDATE `'.$this->bucketName.'` USE KEYS "'.$docName.'" SET conexiones.datos = ARRAY_PUT(IFNULL(conexiones.datos, []), '.$datosJson.')';
            
            $query = \Couchbase\N1qlQuery::fromString($stm);
            //$query->namedParams(['docName' , $docName, 'datos' => $datosJson]);
            $this->bucket->query($query);
        }




    }

    public function borrarTablaAuxiliar($idOrigenDatos) {
        $this->inicializarTablaAuxliar($idOrigenDatos);
    }

    public function inicializarTabla($nombreTabla) {


    }

    public function guardarDatos($idConexion, $idOrigenDatos) {


    }

    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $campoControlIncremento, $limiteInf, $limiteSup){


    }

    public function cargarCatalogo(OrigenDatos $origenDato)
    {

    }
}