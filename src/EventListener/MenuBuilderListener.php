<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace App\EventListener;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * Sonata menu builder.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class MenuBuilderListener
{
    protected $tokenStorage;
    use MenuTrait;
    
    /**
     * Constructor.
     *
     * @param Pool                     $pool
     * @param FactoryInterface         $factory
     * @param MenuProviderInterface    $provider
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    
    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $usuario = $this->tokenStorage->getToken()->getUser();
        
        $menu = $event->getMenu();

        if ( $usuario != 'anon.' ) {
            //Construir menú de varios niveles
            foreach ($menu->getChildren() as $item) {
                // Uso el punto en el nombre del grupo para separar en dos partes,
                // representan los niveles de anidamiento
                $nName = explode('.', $item->getLabel());

                if (count($nName) == 2) {
                    // La segunda parte será la nueva etiqueta
                    $item->setLabel($nName[1]);
                    //Quitar el menú de la raíz
                    $menu->removeChild($item);
                    // Se ingresará el item en el grupo dado por la primera parte
                    $menu[$nName[0]]->addChild($item);
                }
            }


            if ($usuario->hasRole('ROLE_SUPER_ADMIN') or $usuario->hasRole('ROLE_USER_TABLERO')) {

                $this->agregarSiNoExiste('indicadores', $menu);

                $menu['indicadores']->addChild('indicador_tablero', [
                    'label' => '_tablero_',
                    'route' => 'indicadores_tablero'
                ])/*->setExtras([
                    'icon' => '<i class="fa fa-bar-chart"></i>',
                ])*/
                ;
            }

            if ($usuario->hasRole('ROLE_SUPER_ADMIN') or $usuario->hasRole('ROLE_USER_PIVOT_TABLE')) {

                $this->agregarSiNoExiste('indicadores', $menu);

                $menu['indicadores']->addChild('tabla_pivote', [
                    'label' => '_tabla_pivote_',
                    //'route' => 'tablero_pivotTable',
                    'route' => 'pivotTable'
                ])/*->setExtras([
                    'icon' => '<i class="fa fa-bar-chart"></i>',
                ])*/
                ;
            }

            if ($usuario->hasRole('ROLE_SUPER_ADMIN') or $usuario->hasRole('ROLE_MATRIZ_SEGUIMIENTO_ADMIN')) {

                $this->agregarSiNoExiste('matriz_seguimiento', $menu);

                $menu['matriz_seguimiento']->addChild('_config_matriz_anio_', [
                    'label' => '_config_matriz_anio_',
                    'route' => 'matrizPlaneacion'
                ]);
            }

            if ($usuario->hasRole('ROLE_SUPER_ADMIN') or $usuario->hasRole('ROLE_MATRIZ_SEGUIMIENTO_USER') or $usuario->hasRole('ROLE_MATRIZ_SEGUIMIENTO_ADMIN') or $usuario->hasRole('ROLE_MATRIZ_SEGUIMIENTO_USER_CAPTURA')) {

                $this->agregarSiNoExiste('matriz_seguimiento', $menu);

                $menu['matriz_seguimiento']->addChild('_capturar_matriz_', [
                    'label' => '_capturar_matriz_',
                    'route' => 'matrizReal'
                ]);
            }

            if ($usuario->hasRole('ROLE_SUPER_ADMIN') or $usuario->hasRole('ROLE_MATRIZ_SEGUIMIENTO_USER_REPORTE')) {

                $this->agregarSiNoExiste('matriz_seguimiento', $menu);

                $menu['matriz_seguimiento']->addChild('_reporte_', [
                    'label' => '_reporte_',
                    'route' => 'matrizReporte'
                ]);
            }


            $menu->addChild('_ayuda_', [
                'label' => '_documentacion_',
                'route' => 'ayuda'
            ])->setExtras([
                    'icon' => '<i class="fa fa-book"></i>',
            ]);

        }
    }


}
