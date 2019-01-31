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


use App\Entity\UsuarioGrupoIndicadores;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Doctrine\ORM\EntityRepository;

use App\Entity\GrupoIndicadores;
use MINSAL\Bundle\CostosBundle\Entity\Estructura;

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
        $accion = explode('?',$accion);
        $pass_requerido = ($accion == 'create') ? true : false;
        $existeCostosBundle =  array_key_exists('CostosBundle', $this->getConfigurationPool()->getContainer()->getParameter('kernel.bundles') );
            
        

        $now = new \DateTime();

        $formMapper
                ->tab('_usuario_')
                    ->with('General', array('class' => 'col-md-6'))
                        ->add('username')
                        ->add('email')
                        ->add('plainPassword', TextType::class, array(
                            'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
                        ))
                        ->add('enabled', null, array('required' => false))
                    ->end()
                    ->with('_perfil_', array('class' => 'col-md-6'))
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

        if ($existeCostosBundle){

            $formMapper
                ->tab('_usuario_')
                ->with('General')
                ->add('establecimientoPrincipal',
                    EntityType::class, array(
                        'class' => Estructura::class,
                        'label' => '_establecimiento_principal_',
                        'help' => '_ayuda_establecimiento_principal_'
                    ))
                ->end()
                ->end();
        }

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                    ->tab('_seguridad_')
                        
                        ->with('Groups', array('class' => 'col-md-6'))
                            ->add('groups', ModelType::class, array(
                                'required' => false,
                                'expanded' => false,
                                'multiple' => true
                            ))
                        ->end()
                        ->with('Roles', array('class' => 'col-md-6'))
                            ->add('realRoles', SecurityRolesType::class, array(
                                'label' => 'form.label_roles',
                                'expanded' => false,
                                'multiple' => true,
                                'required' => false
                            ))
                        ->end()
                    ->end()
            ;
            //$acciones = explode('/', $this->getRequest()->server->get("REQUEST_URI"));

            if ($accion[0] == 'edit') {

                //Recuperar las salas asignadas al usuario
                $salas = [];
                foreach($this->getSubject()->getGruposIndicadores() as $g){
                    array_push($salas, $g->getGrupoIndicadores() );
                }

                $formMapper
                    ->tab('_indicadores_y_salas_')
                        ->with('_indicadores_', array('class' => 'col-md-6'))
                            ->add('indicadores', null, array(
                                'label' => '_indicadores_',
                                'expanded' => false
                            ))
                        ->end()
                        ->with('_salas_situacionales_', array('class' => 'col-md-6'))
                            ->add('salas', EntityType::class, array(
                                'class' => GrupoIndicadores::class,
                                'label' => '_salas_situacionales_',
                                'query_builder' => function (EntityRepository $er) {
                                    return $er->createQueryBuilder('gi')
                                        ->orderBy('gi.nombre', 'ASC');
                                },
                                'expanded' => false,
                                'multiple' => true,
                                'mapped' => false,
                                'data' => $salas,
                                'attr' => ['data-usuario-id'=> $this->getSubject()->getId()]
                            ))
                        ->end()
                    ->end()
                ;
            }
        }

    }

    public function postUpdate($user): void
    {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $usrActual = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $usuarioEditado = $this->getSubject();

        $salasUsr = [];
        foreach($this->getSubject()->getGruposIndicadores() as $g){
            array_push($salasUsr, $g->getGrupoIndicadores()->getId() );
        }

        $salasFrm = array_map(function($s) use ($salasUsr, $usrActual, $em){
            return $s->getId();
        }, $this->getForm()->get('salas')->getData() );

        array_map(function($s) use ($salasUsr, $usrActual, $em, $usuarioEditado){
            if ( !in_array($s->getId(), $salasUsr)){
                $usrGrupoInd = new UsuarioGrupoIndicadores();
                $usrGrupoInd->setGrupoIndicadores($s);
                $usrGrupoInd->setUsuario($usuarioEditado);
                $usrGrupoInd->setUsuarioAsigno($usrActual);

                $em->persist($usrGrupoInd);
            }
        }, $this->getForm()->get('salas')->getData() );

        array_map(function($s) use ($salasFrm, $usuarioEditado, $em){
            if ( !in_array($s, $salasFrm)){
                $usrGrupoInd = $em->getRepository(UsuarioGrupoIndicadores::class)
                                ->findOneBy(['grupoIndicadores'=>$s, 'usuario' =>$usuarioEditado]);
                if ($usrGrupoInd){
                    $em->remove($usrGrupoInd);
                }
            }
        }, $salasUsr );

        $em->flush();
    }

    public function getTemplate($name) {
        switch ($name) {
            case 'edit':
                return 'CRUD/user-edit.html.twig';
                break;
            default:
                return parent::getTemplateRegistry()->getTemplate($name);
                break;
        }
    }

}
