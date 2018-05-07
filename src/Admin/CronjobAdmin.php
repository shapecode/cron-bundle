<?php

namespace Shapecode\Bundle\CronBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class CronjobAdmin
 *
 * @package Shapecode\Bundle\CronBundle\Admin
 * @author  Nikita Loges
 */
class CronjobAdmin extends AbstractAdmin
{

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('edit');
        $collection->remove('delete');
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('command');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper->addIdentifier('id');
        $listMapper->addIdentifier('fullCommand');
        $listMapper->add('number');
        $listMapper->add('period');
        $listMapper->add('lastUse');
        $listMapper->add('nextRun');
        $listMapper->add('enable', null, [
            'editable' => true
        ]);

        // You may also specify the actions you want to be displayed in the list
        $listMapper->add('_action', null, [
            'actions' => [
                'show' => [],
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('command')
            ->add('arguments')
            ->add('description')
            ->add('number')
            ->add('enable', 'boolean')
            ->add('period')
            ->add('lastUse')
            ->add('nextRun');
    }
}
