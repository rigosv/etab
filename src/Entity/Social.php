<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Social
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\SocialRepository")
 */
class Social
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
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
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=72)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="tiempo_dias", type="integer", length=11)
     */
    private $tiempo_dias;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_permanente", type="boolean")
     */
    private $es_permanente;

	/**
     * @ORM\ManyToOne(targetEntity="GrupoIndicadores")
     * @ORM\JoinColumn(name="sala", referencedColumnName="id")
     * @return integer
     */
    
    private $sala;

	
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
     * Set creado
     *
     * @param \DateTime $creado
     * @return Social
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
     * Set token
     *
     * @param string $token
     * @return Social
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set tiempo_dias
     *
     * @param string $tiempo_dias
     * @return Social
     */
    public function setTiempoDias($tiempo_dias)
    {
        $this->tiempo_dias = $tiempo_dias;
    
        return $this;
    }

    /**
     * Get tiempo_dias
     *
     * @return string 
     */
    public function getTiempoDias()
    {
        return $this->tiempo_dias;
    }

    /**
     * Set es_permanente
     *
     * @param string $es_permanente
     * @return Social
     */
    public function setEsPermanente($es_permanente)
    {
        $this->es_permanente = $es_permanente;
    
        return $this;
    }

    /**
     * Get es_permanente
     *
     * @return string 
     */
    public function getEsPermanente()
    {
        return $this->es_permanente;
    }

    /**
     * Set sala
     *
     * @param integer $sala
     * @return Social
     */
    public function setSala($sala)
    {
        $this->sala = $sala;
    
        return $this;
    }

    /**
     * Get sala
     *
     * @return integer 
     */
    public function getSala()
    {
        return $this->sala;
    }
}
