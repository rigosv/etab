<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * App\Entity\FichaTecnica
 *
 * @ORM\Table(name="ficha_tecnica")
 * @UniqueEntity(fields="codigo", message="Código ya existe")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaRepository")
 */
class FichaTecnica
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
     * @ORM\Column(name="codigo", type="string", length=100, nullable=true)
     */
    private $codigo;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=150, nullable=false)
     */
    private $nombre;

    /**
     * @var string $tema
     *
     * @ORM\Column(name="tema", type="text", nullable=false)
     */
    private $tema;

    /**
     * @var string $concepto
     *
     * @ORM\Column(name="concepto", type="text", nullable=true)
     */
    private $concepto;

    /**
     * @var string $unidadMedida
     *
     * @ORM\Column(name="unidad_medida", type="string", length=50, nullable=false)
     */
    private $unidadMedida;

    /**
     * @var string $formula
     *
     * @ORM\Column(name="formula", type="string", length=300, nullable=false)
     */
    private $formula;

    /**
     * @var string $observacion
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    private $observacion;

    /**
     * @var string $ruta
     *
     * @ORM\Column(name="ruta", type="text", nullable=true)
     */
    private $ruta;

    /**
     * @var string $camposIndicador
     *
     * @ORM\Column(name="campos_indicador", type="text", nullable=true)
     */
    private $camposIndicador;

    /**
     * @var integer $confiabilidad
     *
     * @ORM\Column(name="confiabilidad", type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min = "0",
     *      max = "100"
     * )
     */
    private $confiabilidad;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var boolean $esAcumulado
     *
     * @ORM\Column(name="es_acumulado", type="boolean", nullable=true)
     */
    private $esAcumulado;

    /**
     * @var datetime ultimaLectura
     *
     * @ORM\Column(name="ultima_lectura", type="datetime", nullable=true)
     */
    private $ultimaLectura;


    /**
     * @ORM\ManyToMany(targetEntity="ClasificacionTecnica", inversedBy="indicadores")
     * @ORM\JoinTable(name="fichatecnica_clasificaciontecnica",
     *      joinColumns={@ORM\JoinColumn(name="fichatecnica_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="clasificaciontecnica_id", referencedColumnName="id")}
     * )
     **/
    private $clasificacionTecnica;


    /**
     * @var float $meta
     *
     * @ORM\Column(name="meta", type="float", scale=2, nullable=true)
     */
    private $meta;

    /**
     * @var integer $cantidadDecimales
     *
     * @ORM\Column(name="cantidad_decimales", type="integer", nullable=true)
     */
    private $cantidadDecimales;

    /**
     *
     * @var periodo
     *
     * @ORM\ManyToOne(targetEntity="Periodos")
     * @ORM\JoinColumn(name="id_periodo", referencedColumnName="id")
     * @ORM\OrderBy({"descripcion" = "ASC"})
     **/
    private $periodo;

    /**
     *
     * @var reporte
     *
     * @ORM\ManyToOne(targetEntity="GrupoIndicadores")
     * @ORM\JoinColumn(name="id_sala_reporte", referencedColumnName="id")
     * @ORM\OrderBy({"descripcion" = "ASC"})
     **/
    private $reporte;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="IndicadorAlertas", mappedBy="indicador", cascade={"all"}, orphanRemoval=true)
     *
     */
    private $alertas;

    /**
     * @ORM\ManyToMany(targetEntity="VariableDato", inversedBy="indicadores")
     * @ORM\JoinTable(name="ficha_tecnica_variable_dato",
     *      joinColumns={@ORM\JoinColumn(name="id_ficha_tecnica", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_variable_dato", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"nombre" = "ASC"})
     **/
    private $variables;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="indicadores")
     **/
    private $usuarios;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="indicadores")
     **/
    private $gruposUsuarios;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="favoritos")
     **/
    private $usuariosFavoritos;

    /**
     * @ORM\ManyToMany(targetEntity="Campo")
     * @ORM\JoinTable(name="ficha_tecnica_campo",
     *      joinColumns={@ORM\JoinColumn(name="id_ficha_tecnica", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_campo", referencedColumnName="id")}
     *      )
     **/
    private $campos;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="GrupoIndicadoresIndicador", mappedBy="indicador", cascade={"all"}, orphanRemoval=true)
     */
    private $grupos;

    /**
     *
     * @var agencia
     *
     * @ORM\ManyToOne(targetEntity="Agencia")
     * @ORM\OrderBy({"codigo" = "ASC"})
     **/
    private $agencia;

    /**
     *
     * @var agencia
     *
     * @ORM\ManyToMany(targetEntity="Agencia", mappedBy="indicadores")
     * @ORM\OrderBy({"codigo" = "ASC"})
     **/
    private $agenciasAcceso;

    /**
     * @ORM\ManyToMany(targetEntity="TipoGrafico", cascade={"remove", "persist"})
     * @ORM\JoinTable(name="fichatecnica_tiposgraficos",
     *      joinColumns={@ORM\JoinColumn(name="fichatecnica_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tipografico_id", referencedColumnName="id")}
     *      )
     * */
    private $tiposGraficos;

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
     * Set tema
     *
     * @param  string       $tema
     * @return FichaTecnica
     */
    public function setTema($tema)
    {
        $this->tema = $tema;

        return $this;
    }

    /**
     * Get tema
     *
     * @return string
     */
    public function getTema()
    {
        return $this->tema;
    }

    /**
     * Set concepto
     *
     * @param  string       $concepto
     * @return FichaTecnica
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return string
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    /**
     * Set unidadMedida
     *
     * @param  string       $unidadMedida
     * @return FichaTecnica
     */
    public function setUnidadMedida($unidadMedida)
    {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }

    /**
     * Get unidadMedida
     *
     * @return string
     */
    public function getUnidadMedida()
    {
        return $this->unidadMedida;
    }

    /**
     * Set formula
     *
     * @param  string       $formula
     * @return FichaTecnica
     */
    public function setFormula($formula)
    {
        $this->formula = $formula;

        return $this;
    }

    /**
     * Get formula
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Set observacion
     *
     * @param  string       $observacion
     * @return FichaTecnica
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set confiabilidad
     *
     * @param  integer      $confiabilidad
     * @return FichaTecnica
     */
    public function setConfiabilidad($confiabilidad)
    {
        $this->confiabilidad = $confiabilidad;

        return $this;
    }

    /**
     * Get confiabilidad
     *
     * @return integer
     */
    public function getConfiabilidad()
    {
        return $this->confiabilidad;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->periodos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->variables = new \Doctrine\Common\Collections\ArrayCollection();
        $this->agenciasAcceso = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tiposGraficos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cantidadDecimales = 2;
    }

    /**
     * Add campos
     *
     * @param  App\Entity\Campo $campos
     * @return FichaTecnica
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
        return $this->campos;
    }

    /**
     * Set id
     *
     * @param  integer      $id
     * @return FichaTecnica
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set camposIndicador
     *
     * @param  string       $camposIndicador
     * @return FichaTecnica
     */
    public function setCamposIndicador($camposIndicador)
    {
        $this->camposIndicador = $camposIndicador;

        return $this;
    }

    /**
     * Get camposIndicador
     *
     * @return string
     */
    public function getCamposIndicador()
    {
        return $this->camposIndicador;
    }

    /**
     * Add alertas
     *
     * @param  App\Entity\IndicadorAlertas $alertas
     * @return FichaTecnica
     */
    public function addAlertas(\App\Entity\IndicadorAlertas $alertas)
    {
        //$alertas->setIndicador($this);
        $this->addAlerta($alertas);
        //$this->alertas[] = $alertas;

        //return $this;
    }

    /**
     * Get alertas
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getAlertas()
    {
        return $this->alertas;
    }

    public function __toString()
    {
        return $this->nombre ? :'';
    }

    /**
     * Add alertas
     *
     * @param  App\Entity\IndicadorAlertas $alertas
     * @return FichaTecnica
     */
    public function addAlerta(\App\Entity\IndicadorAlertas $alertas)
    {
        $this->alertas[] = $alertas;

        return $this;
    }

    /**
     * Remove alertas
     *
     * @param MINSAL\IndicadoresBundle\Entity\IndicadorAlertas $alertas
     */
    public function removeAlerta(\App\Entity\IndicadorAlertas $alertas)
    {
        $this->alertas->removeElement($alertas);
    }

    public function removeAlertas()
    {
        $this->alertas=array();
    }

    /**
     * Set updatedAt
     *
     * @param  \DateTime    $updatedAt
     * @return FichaTecnica
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add variables
     *
     * @param  \App\Entity\VariableDato $variables
     * @return FichaTecnica
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
     * Set ultimaLectura
     *
     * @param  \DateTime    $ultimaLectura
     * @return FichaTecnica
     */
    public function setUltimaLectura($ultimaLectura)
    {
        $this->ultimaLectura = $ultimaLectura;

        return $this;
    }

    /**
     * Get ultimaLectura
     *
     * @return \DateTime
     */
    public function getUltimaLectura()
    {
        return $this->ultimaLectura;
    }

    /**
     * Set periodo
     *
     * @param  \App\Entity\Periodos $periodo
     * @return FichaTecnica
     */
    public function setPeriodo(\App\Entity\Periodos $periodo = null)
    {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return \App\Entity\Periodos
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }

    /**
     * Add usuariosFavoritos
     *
     * @param  \App\Entity\User $usuariosFavoritos
     * @return FichaTecnica
     */
    public function addUsuariosFavorito(\App\Entity\User $usuariosFavoritos)
    {
        $this->usuariosFavoritos[] = $usuariosFavoritos;

        return $this;
    }

    /**
     * Remove usuariosFavoritos
     *
     * @param \App\Entity\User $usuariosFavoritos
     */
    public function removeUsuariosFavorito(\App\Entity\User $usuariosFavoritos)
    {
        $this->usuariosFavoritos->removeElement($usuariosFavoritos);
    }

    /**
     * Get usuariosFavoritos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuariosFavoritos()
    {
        return $this->usuariosFavoritos;
    }

    /**
     * Set esAcumulado
     *
     * @param  boolean      $esAcumulado
     * @return FichaTecnica
     */
    public function setEsAcumulado($esAcumulado)
    {
        $this->esAcumulado = $esAcumulado;

        return $this;
    }

    /**
     * Get esAcumulado
     *
     * @return boolean
     */
    public function getEsAcumulado()
    {
        return $this->esAcumulado;
    }

    /**
     * Add grupos
     *
     * @param  \App\Entity\GrupoIndicadoresIndicador $grupos
     * @return FichaTecnica
     */
    public function addGrupo(\App\Entity\GrupoIndicadoresIndicador $grupos)
    {
        $this->grupos[] = $grupos;

        return $this;
    }

    /**
     * Remove grupos
     *
     * @param \App\Entity\GrupoIndicadoresIndicador $grupos
     */
    public function removeGrupo(\App\Entity\GrupoIndicadoresIndicador $grupos)
    {
        $this->grupos->removeElement($grupos);
    }

    /**
     * Get grupos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    /**
     * Add clasificacionTecnica
     *
     * @param  \App\Entity\ClasificacionTecnica $clasificacionTecnica
     * @return FichaTecnica
     */
    public function addClasificacionTecnica(\App\Entity\ClasificacionTecnica $clasificacionTecnica)
    {
        $this->clasificacionTecnica[] = $clasificacionTecnica;

        return $this;
    }

    /**
     * Remove clasificacionTecnica
     *
     * @param \App\Entity\ClasificacionTecnica $clasificacionTecnica
     */
    public function removeClasificacionTecnica(\App\Entity\ClasificacionTecnica $clasificacionTecnica)
    {
        $this->clasificacionTecnica->removeElement($clasificacionTecnica);
    }

    /**
     * Get clasificacionTecnica
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClasificacionTecnica()
    {
        return $this->clasificacionTecnica;
    }

    /**
     * Get clasificacionTecnica
     *
     * @return \App\Entity\ClasificacionTecnica $clasificacionTecnica
     */
    public function setClasificacionTecnica(\Doctrine\Common\Collections\Collection $clasificacionTecnica)
    {
        foreach ($clasificacionTecnica as $c) {
            $this->addClasificacionTecnica($c);
        }
    }

    /**
     * Add usuarios
     *
     * @param  \App\Entity\User $usuarios
     * @return FichaTecnica
     */
    public function addUsuario(\App\Entity\User $usuarios)
    {
        $this->usuarios[] = $usuarios;

        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \App\Entity\User $usuarios
     */
    public function removeUsuario(\App\Entity\User $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Add gruposUsuarios
     *
     * @param \App\Entity\Group $gruposUsuarios
     * @return FichaTecnica
     */
    public function addGruposUsuario(\App\Entity\Group $gruposUsuarios)
    {
        $this->gruposUsuarios[] = $gruposUsuarios;

        return $this;
    }

    /**
     * Remove gruposUsuarios
     *
     * @param \App\Entity\Group $gruposUsuarios
     */
    public function removeGruposUsuario(\App\Entity\Group $gruposUsuarios)
    {
        $this->gruposUsuarios->removeElement($gruposUsuarios);
    }

    /**
     * Get gruposUsuarios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGruposUsuarios()
    {
        return $this->gruposUsuarios;
    }

    /**
     * Set agencia
     *
     * @param \App\Entity\Agencia $agencia
     * @return FichaTecnica
     */
    public function setAgencia(\App\Entity\Agencia $agencia = null)
    {
        $this->agencia = $agencia;

        return $this;
    }

    /**
     * Get agencia
     *
     * @return \App\Entity\Agencia
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Set reporte
     *
     * @param \App\Entity\GrupoIndicadores $reporte
     * @return FichaTecnica
     */
    public function setReporte(\App\Entity\GrupoIndicadores $reporte = null)
    {
        $this->reporte = $reporte;

        return $this;
    }

    /**
     * Get reporte
     *
     * @return \App\Entity\GrupoIndicadores
     */
    public function getReporte()
    {
        return $this->reporte;
    }

    /**
     * Set meta
     *
     * @param float $meta
     * @return FichaTecnica
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get meta
     *
     * @return float
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return FichaTecnica
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set ruta
     *
     * @param string $ruta
     *
     * @return FichaTecnica
     */
    public function setRuta($ruta)
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * Get ruta
     *
     * @return string
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Add agenciaAcceso
     *
     * @param \App\Entity\Agencia $agenciaAcceso
     *
     * @return FichaTecnica
     */
    public function addAgenciaAcceso(\App\Entity\Agencia $agenciaAcceso)
    {
        $this->agenciasAcceso[] = $agenciaAcceso;

        return $this;
    }

    /**
     * Remove agenciaAcceso
     *
     * @param \App\Entity\Agencia $agenciaAcceso
     */
    public function removeAgenciaAcceso(\App\Entity\Agencia $agenciaAcceso)
    {
        $this->agenciasAcceso->removeElement($agenciaAcceso);
    }

    /**
     * Get agenciasAcceso
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgenciasAcceso()
    {
        return $this->agenciasAcceso;
    }

    /**
     * @return int
     */
    public function getCantidadDecimales()
    {
        return $this->cantidadDecimales;
    }

    /**
     * @param int $cantidadDecimales
     */
    public function setCantidadDecimales($cantidadDecimales)
    {
        $this->cantidadDecimales = $cantidadDecimales;
    }

    /**
     * Add tiposGraficos
     *
     * @param  \App\Entity\TipoGrafico $tiposGraficos
     * @return FichaTecnica
     */
    public function addTiposGrafico(\App\Entity\TipoGrafico $tiposGraficos)
    {
        $this->tiposGraficos[] = $tiposGraficos;

        return $this;
    }

    /**
     * Remove tiposGraficos
     *
     * @param \App\Entity\TipoGrafico $tiposGraficos
     */
    public function removeTiposGrafico(\App\Entity\TipoGrafico $tiposGraficos)
    {
        $this->tiposGraficos->removeElement($tiposGraficos);
    }

    /**
     * Get tiposGraficos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTiposGraficos()
    {
        return $this->tiposGraficos;
    }


}
