<?php

namespace App\AlmacenamientoDatos\Driver;

use Doctrine\ORM\EntityManager;

use App\Entity\FichaTecnica;
use App\AlmacenamientoDatos\DashboardInterface;

class PostgreSQLDashboard implements DashboardInterface
{
    private $em;
    private $fichaRepository;


    public function __construct(EntityManager $em, EntityManager $emDatos)
    {
        $this->em = $em;
        $this->fichaRepository = $em->getRepository(FichaTecnica::class);
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