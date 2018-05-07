<?php

namespace Shapecode\Bundle\CronBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class CronjobResultAdmin
 *
 * @package Shapecode\Bundle\CronBundle\Admin
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class CronjobResultAdmin extends AbstractAdmin
{

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('edit');
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('cronJob');
        $datagridMapper->add('statusCode');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper->addIdentifier('id');
        $listMapper->addIdentifier('cronJob');
        $listMapper->add('statusCode');
        $listMapper->add('runAt');
        $listMapper->add('runTime');

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
        $showMapper->add('id');
        $showMapper->add('cronJob');
        $showMapper->add('statusCode');
        $showMapper->add('runAt');
        $showMapper->add('runTime');
        $showMapper->add('output');
    }
}
