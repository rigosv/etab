<?php

namespace App\Entity\MatrizChiapas;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MatrizIndicadoresUsuario
 *
 * @ORM\Table(name="matriz_indicadores_usuario")
 * @ORM\Entity
 */
class MatrizIndicadoresUsuario
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
     * @ORM\ManyToOne(targetEntity="MatrizSeguimientoMatriz")
     * @ORM\JoinColumn(name="id_matriz", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $matriz;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $usuario;

	public function __construct()
    {
		$this->setCreado(new \DateTime());
        $this->setActualizado(new \DateTime());
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
     * Set usuario
     *
     * @param  App\Entity\User $usuario
     * @return User
     */
    public function setUsuario(\App\Entity\User $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return App\Entity\User
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
    
}
