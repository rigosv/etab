<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\FusionOrigenesDatos
 *
 * @ORM\Table(name="fusion_origenes_datos")
 * @ORM\Entity
 */
class FusionOrigenesDatos
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
     * @ORM\ManyToOne(targetEntity="OrigenDatos", inversedBy="fusiones")
     * @ORM\JoinColumn(name="id_origen_datos", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $origenDatos;

    /**
     * @ORM\ManyToOne(targetEntity="OrigenDatos")
     * @ORM\JoinColumn(name="id_origen_datos_fusionado", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $origenDatosFusionado;

    /**
     * @var string $campos
     *
     * @ORM\Column(name="campos", type="text", nullable=true)
     */
    private $campos;

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
     * Set origenDatos
     *
     * @param  App\Entity\OrigenDatos $origenDatos
     * @return FusionOrigenesDatos
     */
    public function setOrigenDatos(\App\Entity\OrigenDatos $origenDatos = null)
    {
        $this->origenDatos = $origenDatos;

        return $this;
    }

    /**
     * Get origenDatos
     *
     * @return App\Entity\OrigenDatos
     */
    public function getOrigenDatos()
    {
        return $this->origenDatos;
    }

    /**
     * Set origenDatosFusionado
     *
     * @param  App\Entity\OrigenDatos $origenDatosFusionado
     * @return FusionOrigenesDatos
     */
    public function setOrigenDatosFusionado(\App\Entity\OrigenDatos $origenDatosFusionado = null)
    {
        $this->origenDatosFusionado = $origenDatosFusionado;

        return $this;
    }

    /**
     * Get origenDatosFusionado
     *
     * @return App\Entity\OrigenDatos
     */
    public function getOrigenDatosFusionado()
    {
        return $this->origenDatosFusionado;
    }

    /**
     * Set campos
     *
     * @param  string              $campos
     * @return FusionOrigenesDatos
     */
    public function setCampos($campos)
    {
        $this->campos = $campos;

        return $this;
    }

    /**
     * Get campos
     *
     * @return string
     */
    public function getCampos()
    {
        return $this->campos;
    }

    /**
     * Set id
     *
     * @param  integer             $id
     * @return FusionOrigenesDatos
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
