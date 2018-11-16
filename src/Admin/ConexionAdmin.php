<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ConexionAdmin extends Admin {

    protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'nombreBaseDatos' // name of the ordered field (default = the model id field, if any)
    );

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
                ->add('nombreConexion', null, array('label' => ('nombre_conexion')))
                ->add('idMotor', null, array('label' => ('motor'),
                    'required' => true))
                ->add('puerto', null, array('label' => ('puerto'), 'required' => false))
                ->add('instancia', null, array('label' => ('instancia'), 'required' => false))
                ->add('ip', null, array('label' => ('ip')))
                ->add('usuario', null, array('label' => ('usuario')))
                ->add('clave', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => ('claves_no_coinciden'),
                    'options' => array('attr' => array('class' => 'span5')),
                    'required' => true,
                    'first_options' => array('label' => ('_clave_')),
                    'second_options' => array('label' => '_repetir_clave_'),
                ))
                ->add('nombreBaseDatos', null, array('label' => ('nombre_base_datos')))
                ->add('comentario', TextareaType::class, array('label' => ('comentario'), 'required' => false))                
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('nombreConexion', null, array('label' => ('nombre_conexion')))
                ->add('idMotor', null, array('label' => ('motor')))
        ;
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('nombreConexion', null, array('label' => ('nombre_conexion')))
                ->add('idMotor', null, array('label' => ('motor')))
                ->add('ip', null, array('label' => ('ip')))
                ->add('nombreBaseDatos', null, array('label' => ('nombre_base_datos')))
                ->add('comentario', null, array('label' => ('comentario')))

        ;
    }

    public function getBatchActions() {
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }

    public function getExportFields() {
        return array('nombreConexion', 'idMotor', 'ip', 'nombreBaseDatos', 'comentario', 'puerto', 'usuario');
    }

    public function getTemplate($name) {
        switch ($name) {
            case 'edit':
                return 'CRUD/conexion-edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

}
