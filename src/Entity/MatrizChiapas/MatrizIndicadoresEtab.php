<?php

namespace App\Entity\MatrizChiapas;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatrizIndicadoresEtab
 *
 * @ORM\Table(name="matriz_indicadores_etab")
 * @ORM\Entity
 */
class MatrizIndicadoresEtab
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
     * @ORM\Column(name="filtros", type="text", nullable=true)
     */
    private $filtros;

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
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\FichaTecnica", inversedBy="ficha")
     * @ORM\JoinColumn(name="id_ficha_tecnica", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     */
    private $ficha;

    /**
     *
     * @ORM\ManyToOne(targetEntity="MatrizIndicadoresDesempeno", inversedBy="desempeno")
     * @ORM\JoinColumn(name="id_desempeno", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     */
    private $desempeno;

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
     * Set filtros
     *
     * @param string $filtros
     * @return MatrizIndicadoresEtab
     */
    public function setFiltros($filtros)
    {
        $this->filtros = $filtros;
    
        return $this;
    }

    /**
     * Get filtros
     *
     * @return string 
     */
    public function getFiltros()
    {
        return $this->filtros;
    }

    /**
     * Set creado
     *
     * @param DateTime $creado
     * @return MatrizIndicadoresEtab
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
     * @return MatrizIndicadoresEtab
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
        return $this->filtros ? :'';
    }


    /**
     * Set desempeno
     *
     * @param  App\Entity\MatrizChiapas\MatrizIndicadoresDesempeno $desempeno
     * @return MatrizIndicadoresEtab
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

    /**
     * Set ficha
     *
     * @param  App\Entity\FichaTecnica $ficha
     * @return MatrizIndicadoresEtab
     */
    public function setFicha(\App\Entity\FichaTecnica $ficha = null)
    {
        $this->ficha = $ficha;

        return $this;
    }
    /**
     * Set ficha
     *
     * @param  App\Entity\FichaTecnica $ficha
     * @return MatrizIndicadoresDesempeno
     */
    public function getFicha(\App\Entity\FichaTecnica $ficha = null)
    {
        $this->ficha = $ficha;

        return $this;
    }

}
