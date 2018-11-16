<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * App\Entity\AccesoExterno
 *
 * @ORM\Table(name="acceso_externo", options={"comment":"Define los accesos a las salas otorgados a personas que no tienen usuario en el sistema"})
 * @UniqueEntity(fields="token", message="token ya existe")
 * @ORM\Entity
 */
class AccesoExterno
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
     * @var string $token
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=false, options={"comment":"El código único que identifica al acceso creado"})
     */
    private $token;
    
    /**
     * @var datetime $caducidad
     *
     * @ORM\Column(name="caducidad", type="datetime", nullable=false, options={"comment":"Fecha en que caducará el acceso"})
     */
    private $caducidad;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\GrupoIndicadores")
     * @ORM\JoinTable(name="accesoexterno_grupoindicadores",
     *      joinColumns={@ORM\JoinColumn(name="accesoexterno_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="grupoindicadores_id", referencedColumnName="id")}
     * )
     **/
    protected $salas;
    
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="usuariocrea_id", referencedColumnName="id")
     * */
    private $usuarioCrea;
    

    

    public function __toString()
    {
        return $this->codigo ? : '';
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->salas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set token
     *
     * @param string $token
     *
     * @return AccesoExterno
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
     * Set caducidad
     *
     * @param \DateTime $caducidad
     *
     * @return AccesoExterno
     */
    public function setCaducidad($caducidad)
    {
        $this->caducidad = $caducidad;

        return $this;
    }

    /**
     * Get caducidad
     *
     * @return \DateTime
     */
    public function getCaducidad()
    {
        return $this->caducidad;
    }

    /**
     * Add sala
     *
     * @param \App\Entity\GrupoIndicadores $sala
     *
     * @return AccesoExterno
     */
    public function addSala(\App\Entity\GrupoIndicadores $sala)
    {
        $this->salas[] = $sala;

        return $this;
    }

    /**
     * Remove sala
     *
     * @param \App\Entity\GrupoIndicadores $sala
     */
    public function removeSala(\App\Entity\GrupoIndicadores $sala)
    {
        $this->salas->removeElement($sala);
    }

    /**
     * Get salas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSalas()
    {
        return $this->salas;
    }

    /**
     * Set usuarioCrea
     *
     * @param \App\Entity\User $usuarioCrea
     *
     * @return AccesoExterno
     */
    public function setUsuarioCrea(\App\Entity\User $usuarioCrea = null)
    {
        $this->usuarioCrea = $usuarioCrea;

        return $this;
    }

    /**
     * Get usuarioCrea
     *
     * @return \App\Entity\User
     */
    public function getUsuarioCrea()
    {
        return $this->usuarioCrea;
    }
}
