<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * App\Entity\Agencia
 *
 * @ORM\Table(name="agencia")
 * @UniqueEntity(fields="codigo", message="Código ya existe")
 * @ORM\Entity
 */
class Agencia
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
     * @ORM\Column(name="codigo", unique=true, type="string", length=20, nullable=false)
     */
    private $codigo;
    
    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=200, nullable=false)
     */
    private $nombre;
    
    /**
     * @ORM\ManyToMany(targetEntity="MINSAL\Bundle\GridFormBundle\Entity\Formulario")
     * @ORM\JoinTable(name="indicador_formulario",
     *      joinColumns={@ORM\JoinColumn(name="id_agencia", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_formulario", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"nombre" = "ASC"})
     **/
    protected $formularios;
    
    /**
     * @ORM\ManyToMany(targetEntity="FichaTecnica", inversedBy="agenciasAcceso")
     * @ORM\JoinTable(name="indicador_agencia",
     *      joinColumns={@ORM\JoinColumn(name="id_agencia", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_indicador", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"nombre" = "ASC"})
     **/
    protected $indicadores;

    

    public function __toString()
    {
        return $this->codigo ? : '';
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
     * Set codigo
     *
     * @param string $codigo
     * @return Agencia
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
     * Set nombre
     *
     * @param string $nombre
     * @return Agencia
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
     * Constructor
     */
    public function __construct()
    {
        $this->indicadores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add indicadores
     *
     * @param \App\Entity\FichaTecnica $indicadores
     * @return Agencia
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

    /**
     * Add formularios
     *
     * @param \MINSAL\Bundle\GridFormBundle\Entity\Formulario $formularios
     * @return Agencia
     */
    public function addFormulario(\MINSAL\Bundle\GridFormBundle\Entity\Formulario $formularios)
    {
        $this->formularios[] = $formularios;

        return $this;
    }

    /**
     * Remove formularios
     *
     * @param \MINSAL\Bundle\GridFormBundle\Entity\Formulario $formularios
     */
    public function removeFormulario(\MINSAL\Bundle\GridFormBundle\Entity\Formulario $formularios)
    {
        $this->formularios->removeElement($formularios);
    }

    /**
     * Get formularios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFormularios()
    {
        return $this->formularios;
    }
}
