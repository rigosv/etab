<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Admin\Model\GroupAdmin as BaseAdmin;
use Sonata\UserBundle\Form\Type\SecurityRolesType;

class GroupAdmin extends BaseAdmin
{    
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper) :void
    {
        $formMapper
            ->tab('_general_')
                ->add('name')
                ->add('roles', SecurityRolesType::class, array(
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false
                ))
            ->end()
        ->end()
        ;
        if ($this->getSubject() and !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN') and $this->isCurrentRoute('edit') ) {
            
            $formMapper
                    ->tab('_indicadores_')
                        ->add('indicadores', null, array('label' => '_indicadores_', 'expanded' => true))
                    ->end()
                ->end()
                ->tab('_salas_')
                    ->add('salas', null, array('label' => '_salas_situacionales_', 'expanded' => true))
                ->end()
            ;
            
        }
    }
    
    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'CRUD/group-edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
}
