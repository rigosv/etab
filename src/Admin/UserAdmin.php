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
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\UserBundle\Form\Type\UserGenderListType;
use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\GrupoIndicadores;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class UserAdmin extends BaseAdmin {
    
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper) : void {
        $listMapper
                ->addIdentifier('username')
                ->add('email')
                ->add('groups')
                ->add('enabled', null, array('editable' => true))
                ->add('locked', null, array('editable' => true))
                ->add('createdAt')
        ;
        /*
          if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
          $listMapper
          ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
          ;
          } */
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper) : void{
        $acciones = explode('/', $this->getRequest()->server->get("REQUEST_URI"));
        $accion = array_pop($acciones);
        $pass_requerido = ($accion == 'create') ? true : false;

        $formMapper
                ->tab('_usuario_')
                    ->with('_perfil_', array('class' => 'col-md-6'))->end()
                    ->with('General', array('class' => 'col-md-6'))->end()
                ->end()
                ->tab('_seguridad_')
                    ->with('_estatus_', array('class' => 'col-md-4'))->end()
                    ->with('Groups', array('class' => 'col-md-4'))->end()
                    ->with('Roles', array('class' => 'col-md-12'))->end()
                ->end()
        ;

        $now = new \DateTime();

        $formMapper
                ->tab('_usuario_')
                    ->with('General')
                        ->add('username')
                        ->add('email')
                        ->add('plainPassword', TextType::class, array(
                            'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
                        ))
                        ->add('establecimientoPrincipal', null, array('label' => '_establecimiento_principal_'))
                    ->end()
                    ->with('_perfil_')
                        ->add('dateOfBirth', DateType::class, array(
                            'years' => range(1900, $now->format('Y')),
                            'required' => false
                        ))
                        ->add('firstname', null, array('required' => false))
                        ->add('lastname', null, array('required' => false))
                        ->add('website', UrlType::class, array('required' => false))
                        ->add('biography', TextType::class, array('required' => false))
                        ->add('gender', ChoiceType::class, array(
                            'required' => true,
                            'translation_domain' => $this->getTranslationDomain(),
                            'choices'  => [
                                'm' => 'm',
                                'f' => 'f',
                                'u' => 'u',
                            ],
                        ))
                        ->add('locale', LocaleType::class, array('required' => false))
                        ->add('timezone', TimezoneType::class, array('required' => false))
                        ->add('phone', null, array('required' => false))
                    ->end()
                ->end()
        ;

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                    ->tab('_seguridad_')
                        ->with('_estatus_')
                            ->add('enabled', null, array('required' => false))
                        ->end()
                        ->with('Groups')
                            ->add('groups', ModelType::class, array(
                                'required' => false,
                                'expanded' => true,
                                'multiple' => true
                            ))
                        ->end()
                        ->with('Roles')
                            ->add('realRoles', SecurityRolesType::class, array(
                                'label' => 'form.label_roles',
                                'expanded' => true,
                                'multiple' => true,
                                'required' => false
                            ))
                        ->end()
                    ->end()
            ;
            $acciones = explode('/', $this->getRequest()->server->get("REQUEST_URI"));
            
            $accion = explode('?',array_pop($acciones));
            if ($accion[0] == 'edit') {
                $formMapper
                    ->tab('_indicadores_y_salas_')
                        ->add('indicadores', null, array('label' => '_indicadores_', 'expanded' => true))
                        ->add('gruposIndicadores', null, array('label' => '_salas_situacionales_',
                            'expanded' => true,
                            'mapped' => false))
                        ->add('salas', EntityType::class, array(
                            'class' => GrupoIndicadores::class,
                            'label' => '_salas_situacionales_',
                            'expanded' => true,
                            'multiple' => true,
                            'mapped' => false
                            ))
                    ->end()
                ;
            }
        }
        $formMapper
                ->setHelps(array(
                    'establecimientoPrincipal' => '_ayuda_establecimiento_principal_'
                ))
        ;
    }

    public function getTemplate($name) {
        switch ($name) {
            case 'edit':
                return 'CRUD/user-edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

}
