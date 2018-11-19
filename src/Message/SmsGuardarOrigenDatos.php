<?php


namespace App\Message;



class SmsGuardarOrigenDatos
{
    private $datos;

    public function __construct( array $datos )
    {
        $this->datos = $datos;
    }

    /**
     * @return array
     */
    public function getDatos(): array
    {
        return $this->datos;
    }
}