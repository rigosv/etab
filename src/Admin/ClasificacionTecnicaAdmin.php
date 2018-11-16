<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ClasificacionTecnicaAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'descripcion' // name of the ordered field (default = the model id field, if any)
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('codigo', null, array('label'=> ('codigo')))
            ->add('descripcion', null, array('label'=> ('descripcion')))
            ->add('comentario', TextareaType::class, array('required'=>false, 'label'=> ('comentario')))
            ->add('clasificacionUso', null, array('required'=>true, 'label'=> ('clasificacion_uso')))

        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('descripcion', null, array('label'=> ('descripcion')))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('descripcion', null, array('label'=> ('descripcion')))
            ->add('comentario', null, array('required'=>false, 'label'=> ('comentario')))
        ;
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }
}
