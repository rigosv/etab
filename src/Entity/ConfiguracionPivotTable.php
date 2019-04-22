<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity\ConfiguracionPivotTable
 *
 * @ORM\Table(name="configuracion_pivot_table")
 * @ORM\Entity(repositoryClass="App\Repository\ConfiguracionPivotTableRepository")
 */
class ConfiguracionPivotTable
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
     * @var string $nombre
     *
     * @ORM\Column(name="tipo_elemento", type="string", length=50, nullable=false)
     */
    private $tipoElemento;

    /**
     * @var integer $idElemento
     *
     * @ORM\Column(name="id_elemento", type="integer", nullable=false)
     */
    private $idElemento;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string $configuracion
     *
     * @ORM\Column(name="configuracion", type="text", nullable=false)
     */
    private $configuracion;



    /**
     * @var boolean $porDefecto
     *
     * @ORM\Column(name="por_defecto", type="boolean", nullable=true)
     */
    private $porDefecto;

    /**
     *
     * @var usuario
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     **/
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
     * Set nombre
     *
     * @param  string       $nombre
     * @return FichaTecnica
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
     * Set configuracion
     *
     * @param  string       $configuracion
     * @return FichaTecnica
     */
    public function setConfiguracion($configuracion)
    {
        $this->configuracion = $configuracion;

        return $this;
    }

    /**
     * Get configuracion
     *
     * @return string
     */
    public function getConfiguracion()
    {
        return $this->configuracion;
    }



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->porDefecto = false;
    }

    /**
     * @return bool
     */
    public function isPorDefecto()
    {
        return $this->porDefecto;
    }

    /**
     * Set porDefecto
     *
     * @param  string       $porDefecto
     * @return FichaTecnica
     */
    public function setPorDefecto($porDefecto)
    {
        $this->porDefecto = $porDefecto;

        return $this;
    }



    /**
     * @return usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param usuario $usuario
     * @return ConfiguracionPivotTable
     */
    public function setUsuario(\App\Entity\User $usuario)
    {
        $this->usuario = $usuario;
        return $this;
    }


    public function __toString()
    {
        return $this->nombre ? :'';
    }


    /**
     * @return string
     */
    public function getTipoElemento(): string
    {
        return $this->tipoElemento;
    }

    /**
     * @param string $tipoElemento
     * @return ConfiguracionPivotTable
     */
    public function setTipoElemento(string $tipoElemento): ConfiguracionPivotTable
    {
        $this->tipoElemento = $tipoElemento;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdElemento(): int
    {
        return $this->idElemento;
    }

    /**
     * @param int $idElemento
     * @return ConfiguracionPivotTable
     */
    public function setIdElemento(int $idElemento): ConfiguracionPivotTable
    {
        $this->idElemento = $idElemento;
        return $this;
    }


}
