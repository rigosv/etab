<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;


/**
 * App\Entity\OrigenDatos
 *
 * @ORM\Table(name="origen_datos")
 * @ORM\Entity(repositoryClass="App\Repository\OrigenDatosRepository")

 */
class OrigenDatos
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     */
    private $nombre;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string $sentenciaSql
     *
     * @ORM\Column(name="sentencia_sql", type="text", nullable=true)
     */
    private $sentenciaSql;

    /**
     * @var Conexiones
     *
     * @ORM\ManyToMany(targetEntity="Conexion", inversedBy="origenes")
     * @ORM\JoinTable(name="origenes_conexiones",
     *      joinColumns={@ORM\JoinColumn(name="origendatos_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="conexion_id", referencedColumnName="id")}
     * )
     */
    private $conexiones;

    /**
     * @var string $archivoNombre
     *
     * @ORM\Column(name="archivo_nombre", type="string", length=255, nullable=true)
     */
    protected $archivoNombre;


    /**
     *
     *
     *
     * @var File
     */
    private $file;

    /**
     * @var string $esFusionado
     *
     * @ORM\Column(name="es_fusionado", type="boolean", nullable=true)
     */
    private $esFusionado;

    /**
     * @var string $esPivote
     *
     * @ORM\Column(name="es_pivote", type="boolean", nullable=true)
     */
    private $esPivote;

    /**
     * @var string $esCatalogo
     *
     * @ORM\Column(name="es_catalogo", type="boolean", nullable=true)
     */
    private $esCatalogo;
        

    /**
     * @var string $nombreCatalogo
     *
     * @ORM\Column(name="nombre_catalogo", type="string", length=100, nullable=true)
     */
    protected $nombreCatalogo;
    
    /**
     * @var integer $minutosUltimaCarga
     *
     * @ORM\Column(name="tiempo_segundos_ultima_carga", type="integer", nullable=true)
     */
    protected $tiempoSegundosUltimaCarga;
    
    /**
     * @var string $cargaFinalizada
     *
     * @ORM\Column(name="carga_finalizada", type="boolean", nullable=true)
     */
    private $cargaFinalizada;
    
    /**
     * @var string $errorCarga
     *
     * @ORM\Column(name="error_carga", type="boolean", nullable=true)
     */
    private $errorCarga;
    
    /**
     * @var string $mensajeErrorCarga
     *
     * @ORM\Column(name="mensaje_error_carga", type="text", nullable=true)
     */
    private $mensajeErrorCarga;

    /**
     * @var string $camposFusionados
     *
     * @ORM\Column(name="campos_fusionados", type="text", nullable=true)
     */
    private $camposFusionados;
    
    /**
     * @ORM\ManyToMany(targetEntity="OrigenDatos")
     * @ORM\JoinTable(name="origen_datos_fusiones",
     *      joinColumns={@ORM\JoinColumn(name="id_origen_dato", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_origen_dato_fusionado", referencedColumnName="id")}
     *      )
     * */
    private $fusiones;
    
    /**
     * @var string $areaCosteo
     *
     * @ORM\Column(name="area_costeo", type="string", length=50, nullable=true)
     */
    protected $areaCosteo;

    /**
     * @ORM\OneToMany(targetEntity="Campo", mappedBy="origenDato")
     */
    private $campos;
    
    /**
     * @ORM\ManyToOne(targetEntity="Campo")
     * @ORM\JoinColumn(name="campolecturaincremental_id", referencedColumnName="id")
     * */
    private $campoLecturaIncremental;

    /**
     * @var string $valorCorte
     *
     * @ORM\Column(name="valor_corte", type="string", length=50, nullable=true)
     */
    private $valorCorte;

    /**
     * @var string $formatoValorCorte
     *
     * @ORM\Column(name="formato_valor_corte", type="string", length=100, nullable=true)
     */
    protected $formatoValorCorte;
    
    /**
     * @var datetime ultimaActualizacion
     *
     * @ORM\Column(name="ultima_actualizacion", type="datetime", nullable=true)
     */
    private $ultimaActualizacion;
    
    /**
     * @var integer $ventana_limite_inferior
     *
     * @ORM\Column(name="ventana_limite_inferior", type="integer", nullable=true)
     * 
     * @Assert\Type(
     *     type="integer"
     * )
     *  @Assert\GreaterThanOrEqual(
     *     value = 0
     * )
     *
     */
    private $ventanaLimiteInferior;
    
    /**
     * @var integer $ventana_limite_superior
     *
     * @ORM\Column(name="ventana_limite_superior", type="integer", nullable=true)
     * 
     * @Assert\Type(
     *     type="integer"
     * )
     *  @Assert\GreaterThanOrEqual(
     *     value = 0
     * )
     *
     */
    private $ventanaLimiteSuperior;


    /**
     * @var string $accionesPoscarga
     *
     * @ORM\Column(name="acciones_poscarga", type="text", nullable=true)
     */
    private $accionesPoscarga;
    

    /**
     * @ORM\OneToMany(targetEntity="VariableDato", mappedBy="origenDatos")
     * */
    private $variables;

    public function __construct()
    {
        $this->fusiones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->conexiones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->esCatalogo = false;
        $this->ventanaLimiteInferior = 0;
        $this->ventanaLimiteSuperior = 0;
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
     * @param  string     $nombre
     * @return TablaDatos
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
     * Set descripcion
     *
     * @param  string     $descripcion
     * @return TablaDatos
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set archivoNombre
     *
     * @param  string      $archivoNombre
     * @return OrigenDatos
     */
    public function setArchivoNombre($archivoNombre)
    {
        $this->archivoNombre = $archivoNombre;

        return $this;
    }

    /**
     * Get archivoNombre
     *
     * @return string
     */
    public function getArchivoNombre()
    {
        return $this->archivoNombre;
    }

    /**
     * Set sentenciaSql
     *
     * @param  string      $sentenciaSql
     * @return OrigenDatos
     */
    public function setSentenciaSql($sentenciaSql)
    {
        $this->sentenciaSql = $sentenciaSql;

        return $this;
    }

    /**
     * Get sentenciaSql
     *
     * @return string
     */
    public function getSentenciaSql()
    {
        return $this->sentenciaSql;
    }

    public function __toString()
    {
        return $this->nombre ? : '';
    }

    /**
     * Set esFusionado
     *
     * @param  boolean     $esFusionado
     * @return OrigenDatos
     */
    public function setEsFusionado($esFusionado)
    {
        $this->esFusionado = $esFusionado;

        return $this;
    }

    /**
     * Get esFusionado
     *
     * @return boolean
     */
    public function getEsFusionado()
    {
        return $this->esFusionado;
    }

    /**
     * Set camposFusionados
     *
     * @param  string      $camposFusionados
     * @return OrigenDatos
     */
    public function setCamposFusionados($camposFusionados)
    {
        $this->camposFusionados = $camposFusionados;

        return $this;
    }

    /**
     * Get camposFusionados
     *
     * @return string
     */
    public function getCamposFusionados()
    {
        return $this->camposFusionados;
    }

    /**
     * Add fusiones
     *
     * @param  App\Entity\OrigenDatos $fusiones
     * @return OrigenDatos
     */
    public function addFusione(\App\Entity\OrigenDatos $fusiones)
    {
        $this->fusiones[] = $fusiones;

        return $this;
    }

    /**
     * Remove fusiones
     *
     * @param MINSAL\IndicadoresBundle\Entity\OrigenDatos $fusiones
     */
    public function removeFusione(\App\Entity\OrigenDatos $fusiones)
    {
        $this->fusiones->removeElement($fusiones);
    }

    /**
     * Get fusiones
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getFusiones()
    {
        return $this->fusiones;
    }

    /**
     * Add campos
     *
     * @param  App\Entity\Campo $campos
     * @return OrigenDatos
     */
    public function addCampo(\App\Entity\Campo $campos)
    {
        $this->campos[] = $campos;

        return $this;
    }

    /**
     * Remove campos
     *
     * @param MINSAL\IndicadoresBundle\Entity\Campo $campos
     */
    public function removeCampo(\App\Entity\Campo $campos)
    {
        $this->campos->removeElement($campos);
    }

    /**
     * Get campos
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCampos()
    {
        $campos = array();
        foreach ($this->campos as $campo) {
            if ($campo->getFormula() == null)
                $campos[] = $campo;
        }

        return $campos;
    }
    /**
     * Get camposCalculados
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCamposCalculados()
    {
        $campos = array();
        foreach ($this->campos as $campo) {
            if ($campo->getFormula() != null)
                $campos[] = $campo;
        }

        return $campos;
    }

    /**
     * Get AllFields
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getAllFields()
    {
        return $this->campos;
    }

    /**
     * Set id
     *
     * @param  integer     $id
     * @return OrigenDatos
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set esCatalogo
     *
     * @param  boolean     $esCatalogo
     * @return OrigenDatos
     */
    public function setEsCatalogo($esCatalogo)
    {
        $this->esCatalogo = $esCatalogo;

        return $this;
    }

    /**
     * Get esCatalogo
     *
     * @return boolean
     */
    public function getEsCatalogo()
    {
        return $this->esCatalogo;
    }

    /**
     * Set nombreCatalogo
     *
     * @param  string      $nombreCatalogo
     * @return OrigenDatos
     */
    public function setNombreCatalogo($nombreCatalogo)
    {
        $this->nombreCatalogo = $nombreCatalogo;

        return $this;
    }

    /**
     * Get nombreCatalogo
     *
     * @return string
     */
    public function getNombreCatalogo()
    {
        return $this->nombreCatalogo;
    }

    /**
     * Add variables
     *
     * @param  \App\Entity\VariableDato $variables
     * @return OrigenDatos
     */
    public function addVariable(\App\Entity\VariableDato $variables)
    {
        $this->variables[] = $variables;

        return $this;
    }

    /**
     * Remove variables
     *
     * @param \App\Entity\VariableDato $variables
     */
    public function removeVariable(\App\Entity\VariableDato $variables)
    {
        $this->variables->removeElement($variables);
    }

    /**
     * Get variables
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Add conexiones
     *
     * @param  \App\Entity\Conexion $conexiones
     * @return OrigenDatos
     */
    public function addConexione(\App\Entity\Conexion $conexiones)
    {
        $this->conexiones[] = $conexiones;

        return $this;
    }

    /**
     * Remove conexiones
     *
     * @param \App\Entity\Conexion $conexiones
     */
    public function removeConexione(\App\Entity\Conexion $conexiones)
    {
        $this->conexiones->removeElement($conexiones);
    }

    /**
     * Get conexiones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConexiones()
    {
        return $this->conexiones;
    }

    /**
     * Set esPivote
     *
     * @param  boolean     $esPivote
     * @return OrigenDatos
     */
    public function setEsPivote($esPivote)
    {
        $this->esPivote = $esPivote;

        return $this;
    }

    /**
     * Get esPivote
     *
     * @return boolean
     */
    public function getEsPivote()
    {
        return $this->esPivote;
    }
    

    /**
     * Set areaCosteo
     *
     * @param string $areaCosteo
     * @return OrigenDatos
     */
    public function setAreaCosteo($areaCosteo)
    {
        $this->areaCosteo = $areaCosteo;

        return $this;
    }

    /**
     * Get areaCosteo
     *
     * @return string 
     */
    public function getAreaCosteo()
    {
        return $this->areaCosteo;
    }

    /**
     * Set campoLecturaIncremental
     *
     * @param \App\Entity\Campo $campoLecturaIncremental
     * @return OrigenDatos
     */
    public function setCampoLecturaIncremental(\App\Entity\Campo $campoLecturaIncremental = null)
    {
        $this->campoLecturaIncremental = $campoLecturaIncremental;

        return $this;
    }

    /**
     * Get campoLecturaIncremental
     *
     * @return \App\Entity\Campo 
     */
    public function getCampoLecturaIncremental()
    {
        return $this->campoLecturaIncremental;
    }

    /**
     * Set ultimaActualizacion
     *
     * @param \DateTime $ultimaActualizacion
     * @return OrigenDatos
     */
    public function setUltimaActualizacion($ultimaActualizacion)
    {
        $this->ultimaActualizacion = $ultimaActualizacion;

        return $this;
    }

    /**
     * Get ultimaActualizacion
     *
     * @return \DateTime 
     */
    public function getUltimaActualizacion()
    {
        return $this->ultimaActualizacion;
    }

    /**
     * Set VentanaLimiteInferior
     *
     * @param integer $ventanaLimiteInferior
     * @return OrigenDatos
     */
    public function setVentanaLimiteInferior($ventanaLimiteInferior)
    {
        $this->ventanaLimiteInferior = $ventanaLimiteInferior;

        return $this;
    }

    /**
     * Get VentanaLimiteInferior
     *
     * @return integer 
     */
    public function getVentanaLimiteInferior()
    {
        return $this->ventanaLimiteInferior;
    }

    /**
     * Set VentanaLimiteSuperior
     *
     * @param integer $ventanaLimiteSuperior
     * @return OrigenDatos
     */
    public function setVentanaLimiteSuperior($ventanaLimiteSuperior)
    {
        $this->ventanaLimiteSuperior = $ventanaLimiteSuperior;

        return $this;
    }

    /**
     * Get VentanaLimiteSuperior
     *
     * @return integer 
     */
    public function getVentanaLimiteSuperior()
    {
        return $this->ventanaLimiteSuperior;
    }


    /**
     * Set tiempoSegundosUltimaCarga
     *
     * @param integer $tiempoSegundosUltimaCarga
     *
     * @return OrigenDatos
     */
    public function setTiempoSegundosUltimaCarga($tiempoSegundosUltimaCarga)
    {
        $this->tiempoSegundosUltimaCarga = $tiempoSegundosUltimaCarga;

        return $this;
    }

    /**
     * Get tiempoSegundosUltimaCarga
     *
     * @return integer
     */
    public function getTiempoSegundosUltimaCarga()
    {
        return $this->tiempoSegundosUltimaCarga;
    }

    /**
     * Set cargaFinalizada
     *
     * @param boolean $cargaFinalizada
     *
     * @return OrigenDatos
     */
    public function setCargaFinalizada($cargaFinalizada)
    {
        $this->cargaFinalizada = $cargaFinalizada;

        return $this;
    }

    /**
     * Get cargaFinalizada
     *
     * @return boolean
     */
    public function getCargaFinalizada()
    {
        return $this->cargaFinalizada;
    }

    /**
     * Set errorCarga
     *
     * @param boolean $errorCarga
     *
     * @return OrigenDatos
     */
    public function setErrorCarga($errorCarga)
    {
        $this->errorCarga = $errorCarga;

        return $this;
    }

    /**
     * Get errorCarga
     *
     * @return boolean
     */
    public function getErrorCarga()
    {
        return $this->errorCarga;
    }

    /**
     * Set mensajeErrorCarga
     *
     * @param string $mensajeErrorCarga
     *
     * @return OrigenDatos
     */
    public function setMensajeErrorCarga($mensajeErrorCarga)
    {
        $this->mensajeErrorCarga = $mensajeErrorCarga;

        return $this;
    }

    /**
     * Get mensajeErrorCarga
     *
     * @return string
     */
    public function getMensajeErrorCarga()
    {
        return $this->mensajeErrorCarga;
    }



    public function setFile( $file = null )
    {
        $this->file = $file;

    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getValorCorte()
    {
        return $this->valorCorte;
    }

    /**
     * @return string
     */
    public function getFormatoValorCorte()
    {
        return $this->formatoValorCorte;
    }

    /**
     * @param datetime string
     */
    public function setValorCorte($valorCorte)
    {
        $this->valorCorte = $valorCorte;
    }

    /**
     * @param string $formatoValorCorte
     */
    public function setFormatoValorCorte($formatoValorCorte)
    {
        $this->formatoValorCorte = $formatoValorCorte;
    }

    /**
     * @return string
     */
    public function getAccionesPoscarga(): ?string
    {
        return $this->accionesPoscarga;
    }

    /**
     * @param string $accionesPoscarga
     */
    public function setAccionesPoscarga(string $accionesPoscarga): void
    {
        $this->accionesPoscarga = $accionesPoscarga;
    }

}
