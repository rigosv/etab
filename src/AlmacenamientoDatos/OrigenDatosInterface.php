<?php

namespace App\AlmacenamientoDatos;

use Doctrine\ORM\EntityManager;

use App\Entity\OrigenDatos;

interface OrigenDatosInterface
{

    /**
     * Verifica si existe la tabla auxiliar, borra los datos en caso afirmativo o la crea
     *
     * @param $idOrigenDatos
     * @return void
     */
    public function inicializarTablaAuxliar($idOrigenDatos);

    /**
     * Guarda la porción de datos leida desde el origen hacia la tabla auxiliar
     *
     * @param $tabla
     * @param $datos
     */
    public function insertarEnAuxiliar($tabla, $datos);

    /**
     * Borra, si existe, la tabla auxiliar
     *
     * @param $tabla
     */
    public function borrarTablaAuxiliar($tabla) ;


    /**
     * Verifica si existe la tabla de destino final, la crea si no existe
     *
     * @param $nombreTabla
     */
    public function inicializarTabla($nombreTabla);


    /**
     * Copia todos los datos de la tabla auxiliar a la tabla de destino final
     *
     * @param $idConexion
     * @param $idOrigenDatos
     */
    public function guardarDatos($idConexion, $idOrigenDatos) ;


    /**
     * Copia de la tabla auxiliar a la tabla destino final, el bloque de datos correspondiente al incremento, basados en un campo de control
     * @param $idConexion
     * @param $idOrigenDatos
     * @param $campoControlIncremento
     * @param $limiteInf
     * @param $limiteSup
     */
    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $campoControlIncremento, $limiteInf, $limiteSup);

    /**
     * Recupera los datos de un origen de datos y los guarda en una tabla catálogo
     *
     * @param OrigenDatos $origenDato
     * @return mixed
     */
    public  function cargarCatalogo(OrigenDatos $origenDato);

}