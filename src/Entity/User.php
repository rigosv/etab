<?php

namespace App\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
   
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="UsuarioGrupoIndicadores", mappedBy="usuario", cascade={"all"}, orphanRemoval=true)
     **/
    protected $gruposIndicadores;
    
     /**
      * @ORM\ManyToMany(targetEntity="FichaTecnica", inversedBy="usuariosFavoritos")
      * @ORM\JoinTable(name="usuario_indicadores_favoritos",
      *      joinColumns={@ORM\JoinColumn(name="id_usuario", referencedColumnName="id", onDelete="CASCADE")},
      *      inverseJoinColumns={@ORM\JoinColumn(name="id_indicador", referencedColumnName="id", onDelete="CASCADE")}
      *      )
      **/
    private $favoritos;

    /**
     * @ORM\ManyToMany(targetEntity="FichaTecnica", inversedBy="usuarios")
     * @ORM\JoinTable(name="indicador_usuario",
     *      joinColumns={@ORM\JoinColumn(name="id_usuario", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_indicador", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"nombre" = "ASC"})
     **/
    protected $indicadores;

    /**
     *
     * @var clasificacionUso
     *
     * @ORM\ManyToOne(targetEntity="ClasificacionUso")
     * @ORM\JoinColumn(name="clasificacionuso_id", referencedColumnName="id")
     * @ORM\OrderBy({"codigo" = "ASC"})
     **/
    private $clasificacionUso;
    
    /**
     *
     * @var agencia
     *
     * @ORM\ManyToOne(targetEntity="Agencia")
     * @ORM\OrderBy({"codigo" = "ASC"})
     **/
    private $agencia;
    
    

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
     * Set id
     *
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $groups;

    /**
     * Add groups
     *
     * @param  \App\Entity\Group $groups
     * @return User
     */
    public function addGroup(GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \App\Entity\Group $groups
     */
    public function removeGroup(GroupInterface $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Gets the groups granted to the user.
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gruposIndicadores = new \Doctrine\Common\Collections\ArrayCollection();
        $this->indicadores = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct();
    }

    /**
     * Add gruposIndicadores
     *
     * @param  App\Entity\UsuarioGrupoIndicadores $gruposIndicadores
     * @return User
     */
    public function addGruposIndicadore(\App\Entity\UsuarioGrupoIndicadores $gruposIndicadores)
    {
        $this->gruposIndicadores[] = $gruposIndicadores;

        return $this;
    }

    /**
     * Remove gruposIndicadores
     *
     * @param App\Entity\UsuarioGrupoIndicadores $gruposIndicadores
     */
    public function removeGruposIndicadore(\App\Entity\UsuarioGrupoIndicadores $gruposIndicadores)
    {
        $this->gruposIndicadores->removeElement($gruposIndicadores);
    }

    /**
     * Get gruposIndicadores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGruposIndicadores()
    {
        return $this->gruposIndicadores;
    }

    /**
     * Add favoritos
     *
     * @param  App\Entity\FichaTecnica $favoritos
     * @return User
     */
    public function addFavorito(\App\Entity\FichaTecnica $favoritos)
    {
        $this->favoritos[] = $favoritos;

        return $this;
    }

    /**
     * Remove favoritos
     *
     * @param App\Entity\FichaTecnica $favoritos
     */
    public function removeFavorito(\App\Entity\FichaTecnica $favoritos)
    {
        $this->favoritos->removeElement($favoritos);
    }

    /**
     * Get favoritos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavoritos()
    {
        return $this->favoritos;
    }

    /**
     * Set clasificacionUso
     *
     * @param  App\Entity\ClasificacionUso $clasificacionUso
     * @return User
     */
    public function setClasificacionUso(\App\Entity\ClasificacionUso $clasificacionUso = null)
    {
        $this->clasificacionUso = $clasificacionUso;

        return $this;
    }

    /**
     * Get clasificacionUso
     *
     * @return App\Entity\ClasificacionUso
     */
    public function getClasificacionUso()
    {
        return $this->clasificacionUso;
    }

    /**
     * Add indicadores
     *
     * @param  App\Entity\FichaTecnica $indicadores
     * @return User
     */
    public function addIndicadore(\App\Entity\FichaTecnica $indicadores)
    {
        $this->indicadores[] = $indicadores;

        return $this;
    }

    /**
     * Remove indicadores
     *
     * @param App\Entity\FichaTecnica $indicadores
     */
    public function removeIndicadore(\App\Entity\FichaTecnica $indicadores)
    {
        $this->indicadores->removeElement($indicadores);
    }

    /**
     * Get indicadores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndicadores()
    {
        return $this->indicadores;
    }

    /**
     * Set agencia
     *
     * @param \MINSAL\IndicadoresBundle\Entity\Agencia $agencia
     * @return User
     */
    public function setAgencia(\App\Entity\Agencia $agencia = null)
    {
        $this->agencia = $agencia;

        return $this;
    }

    /**
     * Get agencia
     *
     * @return App\Entity\Agencia 
     */
    public function getAgencia()
    {
        return $this->agencia;
    }


    /**
     * Set establecimientoPrincipal
     *
     * @param \MINSAL\Bundle\CostosBundle\Entity\Estructura $establecimientoPrincipal
     * @return User
     */
    public function setEstablecimientoPrincipal( $establecimientoPrincipal = null)
    {
        $this->establecimientoPrincipal = $establecimientoPrincipal;

        return $this;
    }

    /**
     * Get establecimientoPrincipal
     *
     * @return \MINSAL\Bundle\CostosBundle\Entity\Estructura
     */
    public function getEstablecimientoPrincipal()
    {
        return $this->establecimientoPrincipal;
    }
    
}
