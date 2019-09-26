<?php

namespace App\Entity\MatrizChiapas;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MatrizIndicadoresDesempeno
 *
 * @ORM\Table(name="matriz_indicadores_desempeno")
 * @ORM\Entity
 */
class MatrizIndicadoresDesempeno
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
     * @ORM\ManyToOne(targetEntity="MatrizSeguimientoMatriz")
     * @ORM\JoinColumn(name="id_matriz", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $matriz;

    /**
     * @var string
     *
     * @ORM\Column(name="orden", type="integer", length=4, nullable=true)
     */
    private $orden;

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
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="MatrizIndicadoresRel", mappedBy="desempeno")
     */
    private $matrizIndicadoresRelacion;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="MatrizIndicadoresEtab", mappedBy="desempeno")
     */
    private $matrizIndicadoresEtab;

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
     * @return MatrizIndicadoresDesempeno
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
     * Set creado
     *
     * @param DateTime $creado
     * @return MatrizIndicadoresDesempeno
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
     * @return MatrizIndicadoresDesempeno
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
     * Set orden
     *
     * @param string $orden
     *
     * @return MatrizSeguimiento
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return string
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set matriz
     *
     * @param  App\Entity\MatrizChiapas\MatrizSeguimientoMatriz $matriz
     * @return MatrizSeguimientoMatriz
     */
    public function setMatriz(\App\Entity\MatrizChiapas\MatrizSeguimientoMatriz $matriz = null)
    {
        $this->matriz = $matriz;

        return $this;
    }

    /**
     * Get matriz
     *
     * @return App\Entity\MatrizChiapas\MatrizSeguimientoMatriz
     */
    public function getMatriz()
    {
        return $this->matriz;
    }

    /**
     * Get matrizIndicadoresRelacion
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMatrizIndicadoresRelacion()
    {
        return $this->matrizIndicadoresRelacion;
    }

    /**
     * Add alertas
     *
     * @param  App\Entity\MatrizChiapas\MatrizIndicadoresRel $matrizIndicadoresRelacion
     * @return MatrizIndicadoresDesempeno
     */
    public function addMatrizIndicadoresRelacion(\App\Entity\MatrizChiapas\MatrizIndicadoresRel $matrizIndicadoresRelacion)
    {
        $this->addMatrizIndicadorRelacion($matrizIndicadoresRelacion);
    }

    /**
     * Add matrizIndicadoresRelacion
     *
     * @param App\Entity\MatrizChiapas\MatrizIndicadoresRel $matrizIndicadoresRelacion
     *
     * @return MatrizIndicadoresDesempeno
     */
    public function addMatrizIndicadorRelacion(\App\Entity\MatrizChiapas\MatrizIndicadoresRel $matrizIndicadoresRelacion)
    {
        $this->matrizIndicadoresRelacion[] = $matrizIndicadoresRelacion;

        return $this;
    }

    /**
     * Remove matrizIndicadoresRelacion
     *
     * @param App\Entity\MatrizChiapas\MatrizIndicadoresRel $matrizIndicadoresRelacion
     */
    public function removeMatrizIndicadorRelacion(\App\Entity\MatrizChiapas\MatrizIndicadoresRel $matrizIndicadoresRelacion)
    {
        $this->matrizIndicadoresRelacion->removeElement($matrizIndicadoresRelacion);
    }

    public function removeMatrizIndicadoresRelacion()
    {
        $this->matrizIndicadoresRelacion=array();
    }

    /**
     * Get matrizIndicadoresEtab
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMatrizIndicadoresEtab()
    {
        return $this->matrizIndicadoresEtab;
    }

    /**
     * Add alertas
     *
     * @param  App\Entity\MatrizChiapas\MatrizIndicadoresEtab $matrizIndicadoresEtab
     * @return MatrizIndicadoresDesempeno
     */
    public function addMatrizIndicadoresEtab(\App\Entity\MatrizChiapas\MatrizIndicadoresEtab $matrizIndicadoresEtab)
    {
        $this->addMatrizIndicadorEtab($matrizIndicadoresEtab);
    }

    /**
     * Add matrizIndicadoresEtab
     *
     * @param App\Entity\MatrizChiapas\MatrizIndicadoresEtab $matrizIndicadoresEtab
     *
     * @return MatrizIndicadoresDesempeno
     */
    public function addMatrizIndicadorEtab(\App\Entity\MatrizChiapas\MatrizIndicadoresEtab $matrizIndicadoresEtab)
    {
        $this->matrizIndicadoresEtab[] = $matrizIndicadoresEtab;

        return $this;
    }

    /**
     * Remove matrizIndicadoresEtab
     *
     * @param App\Entity\MatrizChiapas\MatrizIndicadoresEtab $matrizIndicadoresEtab
     */
    public function removeMatrizIndicadorEtab(\App\Entity\MatrizChiapas\MatrizIndicadoresEtab $matrizIndicadoresEtab)
    {
        $this->matrizIndicadoresEtab->removeElement($matrizIndicadoresEtab);
    }

    public function removeMatrizIndicadoresEtab()
    {
        $this->matrizIndicadoresEtab=array();
    }
}
