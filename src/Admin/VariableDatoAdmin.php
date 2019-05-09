<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\Form\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use App\Entity\OrigenDatos;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class VariableDatoAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'nombre' // name of the ordered field (default = the model id field, if any)
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('origenDatos', EntityType::class, array('label' => 'origen_dato',
                    'class' => OrigenDatos::class,
                    'choice_label' => 'nombre',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('od')
                                ->where('od.esCatalogo = :es_catalogo')
                                ->orderBy('od.nombre', 'ASC')
                                ->setParameter('es_catalogo', 'false');
                    }
                ))
                ->add('nombre', null, array('label' => ('nombre_variable')))
                ->add('iniciales', null, array('label' => ('iniciales')))
                ->add('idFuenteDato', null, array('label' => ('fuente_datos')))
                ->add('idResponsableDato', null, array('label' => ('responsable_datos')))
                ->add('confiabilidad', null, array('label' => ('confiabilidad'), 'required'=>false))
                ->add('comentario', null, array('label' => ('comentario'), 'required'=>false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('nombre', null, array('label' => ('nombre')))
                ->add('iniciales', null, array('label' => ('iniciales')))
                ->add('idResponsableDato', null, array('label' => ('responsable_datos')))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->addIdentifier('nombre', null, array('label' => 'nombre_variable'))
                ->add('iniciales', null, array('label' => ('iniciales')))
                ->add('origenDatos', null, array('label' => ('origen_dato')))

        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $trans = $this->getConfigurationPool()->getContainer()->get('translator');

        $campos_no_configurados = $this->getModelManager()->findBy('App\Entity\Campo',
                array('origenDato' => $object->getOrigenDatos(),
                    'significado' => null));

        if (count($campos_no_configurados) > 0) {
            $errorElement
                ->with('origenDatos')
                    ->addViolation(('origen_no_configurado'))
                ->end();
        }
        if ( !preg_match("/^[0-9a-zA-Z]+$/", $object->getIniciales())) {
            $errorElement
                ->with('iniciales')
                ->addViolation($trans->trans('_solo_caracteres_alfanumericos_') )
                ->end();
        }

    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'CRUD/variable_dato-edit.html.twig';
                break;
            default:
                return parent::getTemplateRegistry()->getTemplate($name);
                break;
        }
    }

}
