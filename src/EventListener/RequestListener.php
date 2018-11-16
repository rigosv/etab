<?php 
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Bitacora;

class RequestListener
{
    /**
     * @var array()
     * Rutas sobre las cuales se llevará una bitácora
     */
    protected $rutas = array(
            'admin_minsal_indicadores_fichatecnica_pivotTable' => 'TABLA_DINAMICA',
            'admin_minsal_indicadores_fichatecnica_tablero' => 'TABLERO_INDICADORES',
            'admin_minsal_gridform_indicador_tableroCalidad' => 'ESTANDARES_CALIDAD',
            'admin_minsal_gridform_indicador_tableroGeneralCalidad' => 'CALIDAD_TABLERO_MANDO',
            'login_normal' => 'INGRESO_SISTEMA',
            'fos_user_security_logout' => 'SALIDA_SISTEMA',
            'admin_minsal_indicadores_salaacciones_create' => 'CREAR_PLAN_ACCION_SALA',
            'sala_set_usuario' => 'COMPARTIR_SALA',
            'fichas_sala' => 'SALA_EXPORTAR_FICHAS_TECNICAS',
            'tablero_sala' => 'SALA_EXPORTAR_GRAFICOS',
            'tablas_datos_sala' => 'SALA_EXPORTAR_TABLA_DE_DATOS',
            'get_grid_data' => 'FORMULARIO_CAPTURA_DATOS_(WEBFORM)',
            'sala_guardar' => 'GUARDAR_SALA_SITUACIONAL'
        );
    
    
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
    */
    protected $_container;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em;

    /**
     * Listener constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * @param string $repo
     * @return Object
     */
    protected function _em($repo = '')
    {
        $em = $this->_em ? : $this->_em = $this->_container->get('doctrine.orm.entity_manager');
        if (!empty($repo)){
            $em = $em->getRepository($repo);
        }
        return $em;
    }
    
    /**
     * @return integer
     */
    protected function _getCurrentUser()
    {
        if ($this->_container->get('security.token_storage')->getToken())
        {
            return $this->_container->get('security.token_storage')->getToken()->getUser();
        }
    }
    
    /**
     * @return 
     */
    protected function _getCurrentSessionId()
    {
        return $this->_container->get('session')->getId();
    }
    
    public function _addLog($accion)
    {
        $bitacora = new Bitacora();
        $bitacora->setAccion($accion);
        $bitacora->setUsuario($this->_getCurrentUser());
        $bitacora->setIdSession($this->_getCurrentSessionId());
        $bitacora->setFechaHora(new \DateTime('NOW'));
        
        $this->_em()->persist($bitacora);
        $this->_em()->flush();
    }
    
    /**
     * kernel.request Event
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request  = $event->getRequest();        
        $ruta = $request->attributes->get('_route');
        
        //echo $ruta;
        
        if ($this->isTrackingRoute($ruta)){
            $this->_addLog($this->rutas[$ruta]);
        }
    }
    
    protected function isTrackingRoute($ruta) {
        return (array_key_exists($ruta, $this->rutas));
    }
}