<?php

namespace App\AlmacenamientoDatos;

use App\Entity\FichaTecnica;

interface DashboardInterface
{

    /**
     * Crea la tabla del indicador a partir de sus variables y orígenes de datos
     *
     * @param FichaTecnica $fichaTec
     * @param $dimension
     * @param $filtros
     */
    public function crearIndicador(FichaTecnica $fichaTec, $dimension =null, $filtros=null);


    /**
     * Con la tabla indicador creada, realiza los cálculos según la fórmula
     *
     * @param $fichaTec
     * @param $dimension
     * @param $filtros
     * @param $verSql
     * @param $filtro_adicional
     * @return mixed
     */
    public function calcularIndicador($fichaTec, $dimension, $filtros, $verSql, $filtro_adicional = '');

    /**
     * Devuelve el resultado de los cálculos estadísticos
     *
     * @param $sql
     * @return mixed
     */

    public function getAnalisisDescriptivo($sql);

    /**
     * Devuelve la cantidad de registros que tiene la tabla del indicador
     *
     * @param FichaTecnica $fichaTec
     * @return mixed
     */

    public function totalRegistrosIndicador(FichaTecnica $fichaTec);


    /**
     * Devuelve los datos del indicador sin procesar la fórmula
     * esto será utilizado en la tabla dinámica
     */
    public function getDatosIndicador(FichaTecnica $fichaTecnica, $offset = 0 , $limit = 100000000);


}