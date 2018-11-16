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


        if ( $usuario->hasRole('ROLE_SUPER_ADMIN') or $usuario->hasRole('ROLE_USER_TABLERO') ) {

            $this->agregarSiNoExiste('indicadores', $menu);

            $menu['indicadores']->addChild('indicador_tablero', [
                'label' => '_tablero_',
                'route' => 'indicadores_tablero'
            ])/*->setExtras([
                'icon' => '<i class="fa fa-bar-chart"></i>',
            ])*/
            ;
        }

        if ( $usuario->hasRole('ROLE_SUPER_ADMIN') or $usuario->hasRole('ROLE_USER_PIVOT_TABLE') ) {

            $this->agregarSiNoExiste('indicadores', $menu);

            $menu['indicadores']->addChild('tabla_pivote', [
                'label' => '_tabla_pivote_',
                'route' => 'pivotTable'
            ])/*->setExtras([
                'icon' => '<i class="fa fa-bar-chart"></i>',
            ])*/
            ;
        }
    }


}
