<?php

namespace App\AlmacenamientoDatos\Driver;

use Doctrine\ORM\EntityManager;

use App\Entity\FichaTecnica;
use App\AlmacenamientoDatos\DashboardInterface;

class CouchbaseDashboard implements DashboardInterface
{
    private $em;
    private $doc = 'origen_';
    private $docIndicador = 'indicador_';
    private $bucketName = 'etab_origenes';
    private $bucket;
    private $bucketNameIndicador = 'etab_indicadores';
    private $bucketIndicador ;

    public function __construct(EntityManager $em, ParameterBagInterface $params)
    {
        $this->em = $em;

        $authenticator = new \Couchbase\PasswordAuthenticator();
        $authenticator->username($params->get('couchbase_user'))->password($params->get('couchbase_password'));

        // Connect to Couchbase Server
        $cluster = new \CouchbaseCluster($params->get('couchbase_url'));

        // Authenticate, then open bucket
        $cluster->authenticate($authenticator);
        $this->bucket = $cluster->openBucket($this->bucketName);
        $this->bucketIndicador = $cluster->openBucket($this->bucketNameIndicador);

    }


    public function crearIndicador(FichaTecnica $fichaTec, $dimension, $filtros) {

        //Verificar si existe el documento de datos del indicador
        $existe = $this->existeDocumento($this->bucketIndicador, $this->docIndicador.$fichaTec->getId());

        // Verificar si la última vez que se cargaron datos para orígenes del indicador es posterior a la última
        // vez que se mostró en el tablero
        if ($fichaTec->getUpdatedAt() != '' and $fichaTec->getUltimaLectura() != '' and $existe == true) {
            if ($fichaTec->getUltimaLectura() < $fichaTec->getUpdatedAt()){
                //Retornar, no es necesario actualizar el documento del indicador
                return true;
            }
        }

        //Los campos de la ficha técnica determinan que campos se utilizarán de los orígenes de datos
        $campos = str_replace(' ', '', $fichaTec->getCamposIndicador());

        //Recuperar los datos de los orígenes asociados a cada variable del indicador
        foreach ($fichaTec->getVariables() as $variable) {
            $origenesIds[] = $variable->getOrigenDatos()->getId();
        }
    }

    public function calcularIndicador($fichaTec, $dimension, $filtros, $verSql){

        return $this->fichaRepository->calcularIndicador($fichaTec, $dimension, $filtros, $verSql);
    }

    public function getAnalisisDescriptivo($sql){
        return $this->fichaRepository->getAnalisisDescriptivo($sql);
    }

    public function totalRegistrosIndicador(FichaTecnica $fichaTec){
        return $this->fichaRepository->totalRegistrosIndicador($fichaTec);
    }

    public function crearCamposIndicador (FichaTecnica $fichaTec) {
        $this->fichaRepository->crearCamposIndicador($fichaTec);
    }

    private function existeDocumento($bucket, $docName){

        $existe = true;
        try {
            $bucket->get( $docName );
        } catch (\Exception $e){
            $existe = false;
        }
        return $existe;
    }
}