<?php

namespace App\AlmacenamientoDatos\Driver;

use App\Entity\OrigenDatos;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\AlmacenamientoDatos\OrigenDatosInterface;
use App\Service\Util;

class CouchbaseOrigenDatos implements OrigenDatosInterface
{
    private $bucket;
    private $em;
    private $doc = 'origen_';
    private $bucketName = 'etab_origenes';

    public function __construct( EntityManager $em, ParameterBagInterface $params)
    {

        $this->em = $em;
        $authenticator = new \Couchbase\PasswordAuthenticator();

        $authenticator->username($params->get('couchbase_user'))->password($params->get('couchbase_password'));

        // Connect to Couchbase Server
        $cluster = new \CouchbaseCluster($params->get('couchbase_url'));

        // Authenticate, then open bucket
        $cluster->authenticate($authenticator);
        $this->bucket = $cluster->openBucket($this->bucketName);

        $this->bucket->operationTimeout = 840 * 100; // 240 segundos

    }


    public function prepararDatosEnvio($idOrigenDatos, $campos_sig, $datos, $ultimaLectura, $idConexion){
        $util = new Util();
        //Cambiar el nombre de las llaves, en lugar de usar el nombre del campo
        // se usará el nombre del significado
        $datos_a_enviar = array();

        foreach ($datos as $fila) {
            $nueva_fila = array();
            foreach ($fila as $k => $v) {
                $v_ = preg_replace("/[\r\n|\n|\r|\t]+/", " ", $v);
                $nueva_fila[$campos_sig[$util->slug($k)]] = trim(mb_check_encoding($v_, 'UTF-8') ? $v_ : mb_convert_encoding($v_, 'UTF-8'));
            }

            $datos_a_enviar[] = $nueva_fila;
        }

        return $datos_a_enviar;
    }

    public function inicializarTablaAuxliar($idOrigenDatos) {
        //Ya no será necesaria esta función
        //$this->borrarTablaAuxiliar($idOrigenDatos, $idConexion);
    }

    public function insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos, $idCarga ) {

        $parte = uniqid();
        $docName = $this->doc . $idOrigenDatos .'_cnx_'.$idConexion. '_parte_'. $parte ;

        $origen = $this->em->find(OrigenDatos::class, $idOrigenDatos);
        $campoIncremental = $origen->getCampoLecturaIncremental();

        $datos_li = 0;
        $datos_ls = 0;
        if ( $campoIncremental != null ) {
            $primero = $datos[0];
            $ultimo = $datos[ count($datos) - 1 ];
            $nombreCampo = $campoIncremental->getSignificado()->getCodigo();

            $datos_li = $primero[$nombreCampo];
            $datos_ls = $ultimo[$nombreCampo];
        }

        $filas = ['id_origen_datos'=>(integer)$idOrigenDatos,
            'id_conexion' => (integer) $idConexion,
            'id_carga' => $idCarga,
            'datos_lim_inf' => $datos_li,
            'datos_lim_sup' => $datos_ls,
            'datos'  => $datos
        ];
        //$this->bucketAux->upsert($docName, $filas);
        //Insertar directo en el bucket para evitar operaciones con grandes volúmenes de datos
        $this->bucket->upsert($docName, $filas);

    }

    public function borrarTablaAuxiliar($idOrigenDatos, $idConexion) {
        //Ya no será necesaria esta función
        //$this->borrarDocumento($this->bucketAuxName, $idOrigenDatos, $idConexion);
    }

    public function inicializarTabla($idOrigenDatos, $idConexion) {
        //No es necesaria está función con couchbase
    }


    public function guardarDatos($idConexion, $idOrigenDatos, $idCarga) {

        //Borrar los datos existentes
        $this->borrarDocumento( $idOrigenDatos, $idConexion, $idCarga);

        /*$stm = 'UPSERT INTO `' . $this->bucketName . '` (KEY _k, VALUE _v) ' .
            ' SELECT META().id _k, _v ' .
            ' FROM `' . $this->bucketAuxName . '` _v' .
            ' WHERE id_origen_datos = ' . $idOrigenDatos . ' AND id_conexion = ' . $idConexion;
        $query = \Couchbase\N1qlQuery::fromString($stm);
        $this->bucket->query($query);
        */
        //Borrar los documentos temporales
        //$this->borrarDocumento($this->bucketAuxName, $idOrigenDatos, $idConexion);
    }

    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $idCarga, $limiteInf, $limiteSup){

        //Qutiar los datos según la condición de lectura incremental
        $origen = $this->em->find(OrigenDatos::class, $idOrigenDatos);

        $campoIncremental = $origen->getCampoLecturaIncremental()->getSignificado()->getCodigo();

        if ($campoIncremental == 'fecha' or $campoIncremental =='date'){
            $li = \DateTime::createFromFormat($origen->getFormatoValorCorte(), $limiteInf)->format('Y-m-d H:i:s');
            $ls = \DateTime::createFromFormat($origen->getFormatoValorCorte(), $limiteSup)->format('Y-m-d H:i:s');
            $corte = $li;
            $stm = 'UPDATE `' . $this->bucket . '` _t 
            SET _t.datos = ARRAY a FOR A IN _t.datos 
                WHEN a.' . $campoIncremental . " <= '" . $corte . "'" . ' END 
            WHERE _t.id_origen_datos= ' . $idOrigenDatos . ' 
            AND _t.id_conexion = ' . $idConexion . "
            AND _t.id_carga != '" . $idCarga . "'".
            ' AND ( ( TONUMBER(_t.datos_lim_inf) <= ' .  $corte .
                    ' AND TONUMBER(_t.datos_lim_sup) >= ' . $corte .
                    ') 
                        OR TONUMBER(_t.datos_lim_inf) > ' . $corte . '
                   )'
                ;
            ;
        } else {
            $corte = $limiteInf;
            $stm = 'UPDATE `' . $this->bucketName . '` _t 
            SET _t.datos = ARRAY a FOR a IN _t.datos 
                WHEN TONUMBER(a.' . $campoIncremental . ") <= " . $corte  .' END 
            WHERE _t.id_origen_datos= ' . $idOrigenDatos . ' 
            AND _t.id_conexion = ' . $idConexion . "
            AND _t.id_carga != '" . $idCarga . "' ".
            ' AND ( ( TONUMBER(_t.datos_lim_inf) <= ' .  $corte .
                    ' AND TONUMBER(_t.datos_lim_sup) >= ' . $corte .
                    ') 
                        OR TONUMBER(_t.datos_lim_inf) > ' . $corte . '
                   )'
                ;

        }

        $query = \Couchbase\N1qlQuery::fromString($stm);
        $this->bucket->query($query);

        //Borrar los documentos que pudieran haber quedado vacíos
        $stmB = 'DELETE FROM `' . $this->bucketName . '` _t                 
                WHERE id_origen_datos= ' . $idOrigenDatos . ' 
                AND id_conexion = ' . $idConexion .
                ' AND ARRAY_COUNT(_t.datos) = 0 '
        ;

        $query = \Couchbase\N1qlQuery::fromString($stmB);
        $this->bucket->query($query);
    }

    private function borrarDocumento( $idOrigenDatos, $idConexion, $idCarga) {
        try {
            $stm = 'DELETE FROM `'.$this->bucketName.'` 
                WHERE id_origen_datos= ' . $idOrigenDatos . ' 
                AND id_conexion = ' . $idConexion . "
                AND id_carga != '" . $idCarga . "'"
                ;
            $query = \Couchbase\N1qlQuery::fromString($stm);
            $this->bucket->query($query);
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }
}