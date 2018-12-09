<?php

namespace App\AlmacenamientoDatos;

use Doctrine\ORM\EntityManager;

use App\Entity\OrigenDatos;

interface OrigenDatosInterface
{

    /**
     * Dar el formato correcpondiente  a los datos que se enviarán para su almacenamiento
     *
     * @param $idOrigenDatos
     * @param $campos_sig Significado de campos utilizados
     * @param $datos
     * @param $ultimaLectura
     * @param $idConexion
     * @return mixed
     */
    public function prepararDatosEnvio($idOrigenDatos, $campos_sig, $datos, $ultimaLectura, $idConexion);

    /**
     * Verifica si existe la tabla auxiliar, borra los datos en caso afirmativo o la crea
     *
     * @param $idOrigenDatos
     * @return void
     */
    public function inicializarTablaAuxliar($idOrigenDatos, $idConexion);

    /**
     * Guarda la porción de datos leida desde el origen hacia la tabla auxiliar
     *
     * @param $tabla
     * @param $datos
     */
    public function insertarEnAuxiliar($idOrigenDatos, $idConexion, $datos, $idCarga);

    /**
     * Borra, si existe, la tabla auxiliar
     *
     * @param $tabla
     */
    public function borrarTablaAuxiliar($idOrigenDatos, $idConexion) ;


    /**
     * Verifica si existe la tabla destino de datos
     *
     * @param $idOrigenDatos
     * @param $idConexion
     * @return mixed
     */
    public function inicializarTabla($idOrigenDatos, $idConexion);


    /**
     * Copia todos los datos de la tabla auxiliar a la tabla de destino final
     *
     * @param $idConexion
     * @param $idOrigenDatos
     */
    public function guardarDatos($idConexion, $idOrigenDatos, $idCarga) ;


    /**
     * Copia de la tabla auxiliar a la tabla destino final, el bloque de datos correspondiente al incremento, basados en un campo de control
     * @param $idConexion
     * @param $idOrigenDatos
     * @param $campoControlIncremento
     * @param $limiteInf
     * @param $limiteSup
     */
    public function guardarDatosIncremental($idConexion, $idOrigenDatos, $idCarga, $limiteInf, $limiteSup);


}