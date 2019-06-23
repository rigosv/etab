<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\VariablesConfiguracion
 *
 * @ORM\Table(name="variables_configuracion")
 * @ORM\Entity(repositoryClass="App\Repository\VariablesConfiguracionRepository")
 */
class VariablesConfiguracion
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string $codigo
     *
     * @ORM\Column(name="codigo", type="string", length=200, nullable=false)
     */
    private $codigo;


    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var string $valor
     *
     * @ORM\Column(name="valor", type="text", nullable=false)
     */
    private $valor;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getValor() {
        return $this->valor;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
        return $this;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
        return $this;
    }

    function setValor($valor) {
        $this->valor = $valor;
        return $this;
    }



}
