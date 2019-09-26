<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity\GrupoIndicadores
 *
 * @ORM\Table(name="grupo_indicadores")
 * @ORM\Entity(repositoryClass="App\Repository\GrupoIndicadoresRepository")
 */
class GrupoIndicadores
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
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=50, nullable=false)
     */
    private $nombre;
    
    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="UsuarioGrupoIndicadores", mappedBy="grupoIndicadores" , cascade={"all"}, orphanRemoval=true)
     **/
    private $usuarios;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="SalaAcciones", mappedBy="sala" , cascade={"all"}, orphanRemoval=true)
     **/
    private $acciones;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="GrupoIndicadoresIndicador", mappedBy="grupo" , cascade={"all"}, orphanRemoval=true)
     **/
    private $indicadores;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="salas")
     **/
    private $grupos;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="ComentariosSala", mappedBy="sala", cascade={"all"}, orphanRemoval=true)
     *
     */
    private $sala;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usuarios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->indicadores = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombre
     *
     * @param  string           $nombre
     * @return GrupoIndicadores
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Add usuarios
     *
     * @param  \App\Entity\UsuarioGrupoIndicadores $usuarios
     * @return GrupoIndicadores
     */
    public function addUsuario(\App\Entity\UsuarioGrupoIndicadores $usuarios)
    {
        $this->usuarios[] = $usuarios;

        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \App\Entity\UsuarioGrupoIndicadores $usuarios
     */
    public function removeUsuario(\App\Entity\UsuarioGrupoIndicadores $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Add indicadores
     *
     * @param  \App\Entity\GrupoIndicadoresIndicador $indicadores
     * @return GrupoIndicadores
     */
    public function addIndicadore(\App\Entity\GrupoIndicadoresIndicador $indicadores)
    {
        $this->indicadores[] = $indicadores;

        return $this;
    }

    /**
     * Remove indicadores
     *
     * @param \App\Entity\GrupoIndicadoresIndicador $indicadores
     */
    public function removeIndicadore(\App\Entity\GrupoIndicadoresIndicador $indicadores)
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
    
    public function __toString()
    {
        return $this->nombre;
    }

    /**
     * Add grupos
     *
     * @param \App\Entity\Group $grupos
     * @return GrupoIndicadores
     */
    public function addGrupo(\App\Entity\Group $grupos)
    {
        $this->grupos[] = $grupos;
    
        return $this;
    }

    /**
     * Remove grupos
     *
     * @param \App\Entity\Group $grupos
     */
    public function removeGrupo(\App\Entity\Group $grupos)
    {
        $this->grupos->removeElement($grupos);
    }

    /**
     * Get grupos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGrupos()
    {
        return $this->grupos;
    }


    /**
     * Add acciones
     *
     * @param \App\Entity\SalaAcciones $acciones
     * @return GrupoIndicadores
     */
    public function addAccione(\App\Entity\SalaAcciones $acciones)
    {
        $this->acciones[] = $acciones;

        return $this;
    }

    /**
     * Remove acciones
     *
     * @param \App\Entity\SalaAcciones $acciones
     */
    public function removeAccione(\App\Entity\SalaAcciones $acciones)
    {
        $this->acciones->removeElement($acciones);
    }

    /**
     * Get acciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAcciones()
    {
        return $this->acciones;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return GrupoIndicadores
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
