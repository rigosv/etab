<?php

namespace App\Entity\MatrizChiapas;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatrizIndicadoresRelAlertas
 *
 * @ORM\Table(name="matriz_indicadores_relacion_alertas")
 * @ORM\Entity
 */
class MatrizIndicadoresRelAlertas
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
     * @var float $limite_inferior
     *
     * @ORM\Column(name="limite_inferior", type="float", scale=2, nullable=true)
     */
    private $limite_inferior;

    /**
     * @var float $limite_superior
     *
     * @ORM\Column(name="limite_superior", type="float", scale=2, nullable=true)
     */
    private $limite_superior;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="text")
     */
    private $color;

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
     * @ORM\ManyToOne(targetEntity="MatrizIndicadoresRel", inversedBy="matrizIndicadoresRelacion_alerta")
     * @ORM\JoinColumn(name="matriz_indicador_relacion_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     */
    private $matriz_indicador;

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
     * Set limite_inferior
     *
     * @param string $limite_inferior
     * @return MatrizIndicadoresRelAlertas
     */
    public function setLimiteInferior($limite_inferior)
    {
        $this->limite_inferior = $limite_inferior;
    
        return $this;
    }

    /**
     * Get limite_inferior
     *
     * @return string 
     */
    public function getLimiteInferior()
    {
        return $this->limite_inferior;
    }

    /**
     * Set limite_superior
     *
     * @param string $limite_superior
     * @return MatrizIndicadoresRelAlertas
     */
    public function setLimiteSuperior($limite_superior)
    {
        $this->limite_superior = $limite_superior;
    
        return $this;
    }

    /**
     * Get limite_superior
     *
     * @return string 
     */
    public function getLimiteSuperior()
    {
        return $this->limite_superior;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return MatrizIndicadoresRelAlertas
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

    /**
     * Set creado
     *
     * @param DateTime $creado
     * @return MatrizIndicadoresRelAlertas
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
     * @return MatrizIndicadoresRelAlertas
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
        return $this->color ? :'';
    }


    /**
     * Set matriz_indicador
     *
     * @param  App\Entity\MatrizChiapas\MatrizIndicadoresRel $matriz_indicador
     * @return MatrizIndicadoresEtabAlertas
     */
    public function setMatrizIndicador(\App\Entity\MatrizChiapas\MatrizIndicadoresRel $matriz_indicador = null)
    {
        $this->matriz_indicador = $matriz_indicador;

        return $this;
    }
    /**
     * Set matriz_indicador
     *
     * @param  App\Entity\MatrizChiapas\MatrizIndicadoresRel $matriz_indicador
     * @return MatrizIndicadoresRelAlertas
     */
    public function getMatrizIndicador(\App\Entity\MatrizChiapas\MatrizIndicadoresRel $matriz_indicador = null)
    {
        $this->matriz_indicador = $matriz_indicador;

        return $this;
    }

}
