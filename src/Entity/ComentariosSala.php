<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\UsuarioGrupoIndicadores
 *
 * @ORM\Table(name="sala_comentarios")
 * @ORM\Entity
 */
class ComentariosSala
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
     * @var $sala
     *
     * @ORM\ManyToOne(targetEntity="GrupoIndicadores", inversedBy="sala", cascade={"persist"})
     * @ORM\JoinColumn(name="id_origen_datos", referencedColumnName="id", nullable=false)
     *
     */
    private $sala;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="gruposIndicadores")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;
    

    /**
     * @var string $comentario
     *
     * @ORM\Column(name="comentario", type="text", nullable=false)
     */
    private $comentario;
    
    /**
     * @var datetime $fecha
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;
    
    public function __toString()
    {
        return $this->comentario;
    }

    /**
     * Set comentario
     *
     * @param string $comentario
     * @return ComentariosSala
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

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return ComentariosSala
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
     * @return ComentariosSala
     */
    public function setSala(\App\Entity\GrupoIndicadores $sala)
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
     * @return ComentariosSala
     */
    public function setUsuario(\App\Entity\User $usuario)
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

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
