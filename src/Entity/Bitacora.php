<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Bitacora
 *
 * @ORM\Table(name="bitacora", options={"comment": "Registro de las acciones realizadas por los usuarios"})
 * @ORM\Entity(repositoryClass="App\Repository\BitacoraRepository")
 */
class Bitacora
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
     * @ORM\Column(name="id_session", type="string", length=100, nullable=false, options={"comment":"Código identificador de la sesión, tomando del identificador dado por PHP"})
     */
    private $idSession;


    /**
     * @var datetime $fechaHora
     *
     * @ORM\Column(name="fecha_hora", type="datetime", nullable=false, options={"comment":"Fecha y hora en que se realizó la acción"})
     */
    private $fechaHora;

    /**
     * @var string $accion
     *
     * @ORM\Column(name="accion", type="string", length=100, nullable=false, options={"comment":"Nombre descriptivo de la acción realizada: INGRESO_SISTEMA, FORMULARIO_CAPTURA, TABLERO, etc."})
     */
    private $accion;
    
    /**
     * @var string $elemento
     *
     * @ORM\Column(name="elemento", type="text", nullable=true, options={"comment":"Elemento con el cual se interactuó"})
     */
    private $elemento;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="gruposIndicadores")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     */
    private $usuario;

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
     * Set idSession
     *
     * @param string $idSession
     *
     * @return Bitacora
     */
    public function setIdSession($idSession)
    {
        $this->idSession = $idSession;

        return $this;
    }

    /**
     * Get idSession
     *
     * @return string
     */
    public function getIdSession()
    {
        return $this->idSession;
    }

    /**
     * Set fechaHora
     *
     * @param \DateTime $fechaHora
     *
     * @return Bitacora
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    /**
     * Get fechaHora
     *
     * @return \DateTime
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    /**
     * Set accion
     *
     * @param string $accion
     *
     * @return Bitacora
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;

        return $this;
    }

    /**
     * Get accion
     *
     * @return string
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set usuario
     *
     * @param \App\Entity\User $usuario
     *
     * @return Bitacora
     */
    public function setUsuario(\App\Entity\User $usuario = null)
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

    /**
     * Set elemento
     *
     * @param string $elemento
     *
     * @return Bitacora
     */
    public function setElemento($elemento)
    {
        $this->elemento = $elemento;

        return $this;
    }

    /**
     * Get elemento
     *
     * @return string
     */
    public function getElemento()
    {
        return $this->elemento;
    }
}
