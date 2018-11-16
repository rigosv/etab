<?php

namespace App\EventListener;


use Knp\Menu\ItemInterface;

trait MenuTrait
{
    /**
     * Agrega un elemento al menú en caso que no exista
     * @param $item
     * @param $menu
     * @param null $superior
     * @param null $icon
     * @return void
     */
    private function agregarSiNoExiste($item, ItemInterface $menu, $superior = null, $icon = null) : void {
        $menuX = ( $superior == null ) ? $menu : $menu[$superior] ;
        if ( $menuX->getChild($item) == null ){
            $menu->addChild($item, ['label' => $item])
                ->setExtras([
                    'icon' => ($icon == null ) ? '<i class="fa fa-folder"></i>' : $icon
                ])
            ;
        }


    }
}