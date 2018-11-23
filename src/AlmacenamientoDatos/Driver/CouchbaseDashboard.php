<?php

namespace App\AlmacenamientoDatos\Driver;

use Doctrine\ORM\EntityManager;

use App\Entity\FichaTecnica;
use App\AlmacenamientoDatos\DashboardInterface;

class CouchbaseDashboard implements DashboardInterface
{
    private $em;
    private $doc = 'origen_';
    private $bucketName = 'etab_origenes';
    private $bucket;
    private $bucketNameIndicadores = 'etab_indicadores';
    private $bucketIndicadores ;

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
        $this->bucketIndicadores = $cluster->openBucket($this->bucketNameIndicadores);

    }


    public function crearIndicador(FichaTecnica $fichaTec, $dimension, $filtros) {

        $this->fichaRepository->crearIndicador($fichaTec, $dimension, $filtros);
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
}