<?php

namespace App\AlmacenamientoDatos;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\FichaTecnica;
use App\Entity\OrigenDatos;
use App\AlmacenamientoDatos\OrigenDatosInterface;
use App\AlmacenamientoDatos\DashboardInterface;
use App\AlmacenamientoDatos\Driver\PostgreSQLDashboard;
use App\AlmacenamientoDatos\Driver\PostgreSQLOrigenDatos;



class AlmacenamientoProxy implements DashboardInterface, OrigenDatosInterface
{
    private $params;
    private $em;
    private $dashboardWrapped;
    private $origenDatosWrapped;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->params = $params;

        //Por defecto es PostgresSQL
        if ($params->get('app.datos.tipo_almacenamiento') == 'couchbase'){
            //$this->wrapped =
        } else {
            $this->dashboardWrapped = new PostgreSQLDashboard($em);
            $this->origenDatosWrapped = new PostgreSQLOrigenDatos($em);
        }
    }

    // **** MÉTODOS PARA LOS ORÍGENES DE DATOS

    public function inicializarTablaAuxliar($idOrigenDatos){
        $this->origenDatosWrapped->inicializarTablaAuxliar($idOrigenDatos);
    }

    public function insertarEnAuxiliar($tabla, $datos){
        $this->origenDatosWrapped->insertarEnAuxiliar($tabla, $datos);
    }


    public function borrarTablaAuxiliar($tabla) {
        $this->origenDatosWrapped->borrarTablaAuxiliar($tabla);
    }


    public function inicializarTabla($nombreTabla){
        $this->origenDatosWrapped->inicializarTabla($nombreTabla);
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
        $this->dashboardWrapped->calcularIndicador($fichaTec, $dimension, $filtros, $verSql);
    }


    public function getAnalisisDescriptivo($sql){
        $this->dashboardWrapped->getAnalisisDescriptivo($sql);
    }


    public function totalRegistrosIndicador(FichaTecnica $fichaTec){
        $this->dashboardWrapped->totalRegistrosIndicador($fichaTec);
    }




}