<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Alerta
 *
 * @ORM\Table(name="alerta", options={"comment":"Identifica los colores que se utilizarán en las alertas de los indicadores"})
 * @ORM\Entity
 */
class Alerta
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
     * @ORM\Column(name="codigo", type="string", length=30, nullable=false, options={"comment":"Código que identificará el color de la alerta"})
     */
    private $codigo;

    /**
     * @var string $color
     *
     * @ORM\Column(name="color", type="string", length=50, unique=true, nullable=false, options={"comment":"Nombre que identificará al color"})
     */
    private $color;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set color
     *
     * @param  string $color
     * @return Alerta
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    public function __toString()
    {
        return $this->color ? :'';
    }

    /**
     * Set codigo
     *
     * @param  string $codigo
     * @return Alerta
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }
}
