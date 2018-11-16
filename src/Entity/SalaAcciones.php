<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\UsuarioGrupoIndicadores
 *
 * @ORM\Table(name="sala_acciones")
 * @ORM\Entity
 */
class SalaAcciones
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
     *
     * @ORM\ManyToOne(targetEntity="GrupoIndicadores", inversedBy="acciones")
     * @ORM\JoinColumn(name="grupo_indicadores_id", referencedColumnName="id")
     */
    private $sala;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;
    

    /**
     * @var string $acciones
     *
     * @ORM\Column(name="acciones", type="text", nullable=false)
     */
    private $acciones;
    
    /**
     * @var string $observaciones
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;
    
    /**
     * @var string $responsables
     *
     * @ORM\Column(name="responsables", type="text", nullable=true)
     */
    private $responsables;
    
    /**
     * @var datetime $fecha
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;
    
    public function __toString()
    {
        return $this->acciones;
    }

    

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
     * Set acciones
     *
     * @param string $acciones
     * @return SalaAcciones
     */
    public function setAcciones($acciones)
    {
        $this->acciones = $acciones;

        return $this;
    }

    /**
     * Get acciones
     *
     * @return string 
     */
    public function getAcciones()
    {
        return $this->acciones;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return SalaAcciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set responsables
     *
     * @param string $responsables
     * @return SalaAcciones
     */
    public function setResponsables($responsables)
    {
        $this->responsables = $responsables;

        return $this;
    }

    /**
     * Get responsables
     *
     * @return string 
     */
    public function getResponsables()
    {
        return $this->responsables;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return SalaAcciones
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set sala
     *
     * @param \App\Entity\GrupoIndicadores $sala
     * @return SalaAcciones
     */
    public function setSala(\App\Entity\GrupoIndicadores $sala = null)
    {
        $this->sala = $sala;

        return $this;
    }

    /**
     * Get sala
     *
     * @return \App\Entity\GrupoIndicadores 
     */
    public function getSala()
    {
        return $this->sala;
    }

    /**
     * Set usuario
     *
     * @param \App\Entity\User $usuario
     * @return SalaAcciones
     */
    public function setUsuario(\App\Entity\User $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \App\Entity\User 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
