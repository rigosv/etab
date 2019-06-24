<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class VariablesConfiguracionAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'codigo' // name of the ordered field (default = the model id field, if any)
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('codigo', null, array('label' => '_codigo_'))
                ->add('descripcion', null, array('label' => '_descripcion_', 'required' => false))
                ->add('valor', null, array('label' => '_valor_', 'required' => true))
                ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('codigo', null, array('label' => '_codigo_'))
                ->add('descripcion', null, array('label' => '_descripcion_', 'required' => false))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('codigo', null, array('label' => '_codigo_'))
                ->add('descripcion', null, array('label' => '_descripcion_', 'required' => false))
                ->add('valor', null, array('label' => '_valor_', 'required' => true))
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array()
                    )
                ))
        ;
    }

}
