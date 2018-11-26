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
    public function crearIndicador(FichaTecnica $fichaTec, $dimension, $filtros);


    /**
     * Con la tabla indicador creada, realiza los cálculos según la fórmula
     *
     * @param $fichaTec
     * @param $dimension
     * @param $filtros
     * @param $verSql
     * @return mixed
     */
    public function calcularIndicador($fichaTec, $dimension, $filtros, $verSql);

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


}