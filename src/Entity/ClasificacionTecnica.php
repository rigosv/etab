<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\ClasificacionTecnica
 *
 * @ORM\Table(name="clasificacion_tecnica")
 * @ORM\Entity
 */
class ClasificacionTecnica
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
     * @ORM\Column(name="codigo", type="string", length=50, nullable=false)
     */
    private $codigo;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="string", length=350, nullable=false)
     */
    private $descripcion;

    /**
     * @var string $comentario
     *
     * @ORM\Column(name="comentario", type="text", nullable=true)
     */
    private $comentario;

    /**
     * @ORM\ManyToMany(targetEntity="FichaTecnica", mappedBy="clasificacionTecnica")
     **/
    private $indicadores;

  /**
     *
     * @var clasificacionUso
     *
     * @ORM\ManyToOne(targetEntity="ClasificacionUso")
     * @ORM\JoinColumn(name="clasificacionuso_id", referencedColumnName="id")
     * @ORM\OrderBy({"codigo" = "ASC"})
     **/
    private $clasificacionUso;

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
     * Set descripcion
     *
     * @param  string               $descripcion
     * @return ClasificacionTecnica
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set comentario
     *
     * @param  string               $comentario
     * @return ClasificacionTecnica
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    public function __toString()
    {
        if ($this->clasificacionUso)
            return $this->clasificacionUso->getDescripcion().' -- '.$this->descripcion;
        else
            return ' -- '.$this->descripcion;
    }

    /**
     * Set codigo
     *
     * @param  string               $codigo
     * @return ClasificacionTecnica
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

    /**
     * Set id
     *
     * @param  integer              $id
     * @return ClasificacionTecnica
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set clasificacionUso
     *
     * @param  \App\Entity\ClasificacionUso $clasificacionUso
     * @return ClasificacionTecnica
     */
    public function setClasificacionUso(\App\Entity\ClasificacionUso $clasificacionUso = null)
    {
        $this->clasificacionUso = $clasificacionUso;

        return $this;
    }

    /**
     * Get clasificacionUso
     *
     * @return \App\Entity\ClasificacionUso
     */
    public function getClasificacionUso()
    {
        return $this->clasificacionUso;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->indicadores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add indicadores
     *
     * @param  \App\Entity\FichaTecnica $indicadores
     * @return ClasificacionTecnica
     */
    public function addIndicadore(\App\Entity\FichaTecnica $indicadores)
    {
        $this->indicadores[] = $indicadores;

        return $this;
    }

    /**
     * Remove indicadores
     *
     * @param \App\Entity\FichaTecnica $indicadores
     */
    public function removeIndicadore(\App\Entity\FichaTecnica $indicadores)
    {
        $this->indicadores->removeElement($indicadores);
    }

    /**
     * Get indicadores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndicadores()
    {
        return $this->indicadores;
    }
}
