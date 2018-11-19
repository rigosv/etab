<?php


namespace App\Message;




class SmsCargarOrigenDatos
{
    private $idOrigenDatos;

    public function __construct( $idOrigenDatos )
    {
        $this->idOrigenDatos = $idOrigenDatos;
    }


    /**
     * @return mixed
     */
    public function getIdOrigenDatos()
    {
        return $this->idOrigenDatos;
    }
}