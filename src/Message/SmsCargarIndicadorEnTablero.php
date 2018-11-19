<?php


namespace App\Message;



class SmsCargarIndicadorEnTablero
{
    private $idIndicador;

    public function __construct( $idIndicador )
    {
        $this->idIndicador = $idIndicador;
    }

    /**
     * @return mixed
     */
    public function getIdIndicador()
    {
        return $this->idIndicador;
    }
}