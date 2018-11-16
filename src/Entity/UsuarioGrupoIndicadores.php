<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\UsuarioGrupoIndicadores
 *
 * @ORM\Table(name="usuario_grupo_indicadores")
 * @ORM\Entity
 */
class UsuarioGrupoIndicadores
{
    /**
     *
     * @ORM\ManyToOne(targetEntity="GrupoIndicadores", inversedBy="usuarios")
     * @ORM\JoinColumn(name="grupo_indicadores_id", referencedColumnName="id")
     * @ORM\Id
     */
    private $grupoIndicadores;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="gruposIndicadores")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * @ORM\Id
     */
    private $usuario;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="usuario_asigno_id", referencedColumnName="id")
     * El usuario que asigno la sala a otro usuario
     */
    private $usuarioAsigno;

    /**
     * @var string $esDuenio
     *
     * @ORM\Column(name="es_duenio", type="boolean", nullable=true)
     */
    private $esDuenio;

    /**
     * Set esDuenio
     *
     * @param  boolean                 $esDuenio
     * @return UsuarioGrupoIndicadores
     */
    public function setEsDuenio($esDuenio)
    {
        $this->esDuenio = $esDuenio;

        return $this;
    }

    /**
     * Get esDuenio
     *
     * @return boolean
     */
    public function getEsDuenio()
    {
        return $this->esDuenio;
    }

    /**
     * Set grupoIndicadores
     *
     * @param  \App\Entity\GrupoIndicadores $grupoIndicadores
     * @return UsuarioGrupoIndicadores
     */
    public function setGrupoIndicadores(\App\Entity\GrupoIndicadores $grupoIndicadores)
    {
        $this->grupoIndicadores = $grupoIndicadores;

        return $this;
    }

    /**
     * Get grupoIndicadores
     *
     * @return \App\Entity\GrupoIndicadores
     */
    public function getGrupoIndicadores()
    {
        return $this->grupoIndicadores;
    }

    /**
     * Set usuario
     *
     * @param  \App\Entity\User $usuario
     * @return UsuarioGrupoIndicadores
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
    
    public function __toString()
    {
        return $this->grupoIndicadores->getNombre();
    }

    /**
     * Set usuarioAsigno
     *
     * @param \App\Entity\User $usuarioAsigno
     * @return UsuarioGrupoIndicadores
     */
    public function setUsuarioAsigno(\App\Entity\User $usuarioAsigno)
    {
        $this->usuarioAsigno = $usuarioAsigno;
    
        return $this;
    }

    /**
     * Get usuarioAsigno
     *
     * @return \App\Entity\User 
     */
    public function getUsuarioAsigno()
    {
        return $this->usuarioAsigno;
    }
}
