<?php

namespace App\Entity\MatrizChiapas;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatrizIndicadoresRel
 *
 * @ORM\Table(name="matriz_indicadores_relacion")
 * @ORM\Entity
 */
class MatrizIndicadoresRel
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=500)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="fuente", type="string", length=500, nullable=true)
     */
    private $fuente;

    /**
     * @var string
     *
     * @ORM\Column(name="es_formula", type="boolean", nullable=true)
     */
    private $es_formula;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime")
     */
    private $creado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="actualizado", type="datetime")
     */
    private $actualizado;

    /**
     * @ORM\ManyToOne(targetEntity="MatrizIndicadoresDesempeno", inversedBy="matrizIndicadoresRelacion")
     * @ORM\JoinColumn(name="id_desempeno", referencedColumnName="id", nullable=false)
     */
    private $desempeno;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="MatrizIndicadoresRelAlertas", mappedBy="matriz_indicador", cascade={"all"}, orphanRemoval=true)
     *
     */
    private $alertas;

	public function __construct()
    {
        $this->setCreado(new \DateTime());
        $this->setActualizado(new \DateTime());
    }
	/**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
       $this->setActualizado(new \DateTime());
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
     * @param string $nombre
     * @return MatrizIndicadoresRel
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
     * Set es_formula
     *
     * @param string $es_formula
     * @return MatrizIndicadoresRel
     */
    public function setEsFormula($es_formula)
    {
        $this->es_formula = $es_formula;

        return $this;
    }

    /**
     * Get es_formula
     *
     * @return string 
     */
    public function getEsFormula()
    {
        return $this->es_formula;
    }

    /**
     * Set fuente
     *
     * @param string $fuente
     * @return MatrizIndicadoresRel
     */
    public function setFuente($fuente)
    {
        $this->fuente = $fuente;
    
        return $this;
    }

    /**
     * Get fuente
     *
     * @return string 
     */
    public function getFuente()
    {
        return $this->fuente;
    }

    /**
     * Set creado
     *
     * @param DateTime $creado
     * @return MatrizIndicadoresRel
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;
    
        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime 
     */
    public function getCreado()
    {
        return $this->creado;
    }

    /**
     * Set actualizado
     *
     * @param DateTime $actualizado
     * @return MatrizIndicadoresRel
     */
    public function setActualizado($actualizado)
    {
        $this->actualizado = $actualizado;
    	
        return $this;
    }

    /**
     * Get actualizado
     *
     * @return \DateTime 
     */
    public function getActualizado()
    {
        return $this->actualizado;
    }
	
	public function __toString() {
        return $this->nombre ? :'';
    }


    /**
     * Set desempeno
     *
     * @param  App\Entity\MatrizChiapas\MatrizIndicadoresDesempeno $desempeno
     * @return MatrizIndicadoresRel
     */
    public function setDesempeno(\App\Entity\MatrizChiapas\MatrizIndicadoresDesempeno $desempeno = null)
    {
        $this->desempeno = $desempeno;

        return $this;
    }
    /**
     * Set desempeno
     *
     * @param  App\Entity\MatrizChiapas\MatrizIndicadoresDesempeno $desempeno
     * @return MatrizIndicadoresDesempeno
     */
    public function getDesempeno(\App\Entity\MatrizChiapas\MatrizIndicadoresDesempeno $desempeno = null)
    {
        $this->desempeno = $desempeno;

        return $this;
    }

}
