<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Dish for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Dish;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Dish\Model\Dish;
use Dish\Model\DishTable;
use Dish\Model\Dishgroup;
use Dish\Model\DishgroupTable;
use Dish\Model\TakeoutDish;
use Dish\Model\TakeoutDishTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    // Add this method:
    public function getServiceConfig() {
    
        return array(
            'factories' => array(
                'Dish\Model\DishTable' =>  function($sm) {
                    $tableGateway = $sm->get('DishTableGateway');
                    $table = new DishTable($tableGateway);
                    return $table;
                },
                'DishTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Dish());
                    return new TableGateway('dish', $dbAdapter, null, $resultSetPrototype);
                },
                'Dish\Model\DishgroupTable' =>  function($sm) {
                    $tableGateway = $sm->get('DishgroupTableGateway');
                    $table = new DishgroupTable($tableGateway);
                    return $table;
                },
                'DishgroupTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Dishgroup());
                    return new TableGateway('dishgroup', $dbAdapter, null, $resultSetPrototype);
                },   
                'Dish\Model\TakeoutDishTable' =>  function($sm) {
                    $tableGateway = $sm->get('TakeoutDishTableGateway');
                    $table = new TakeoutDishTable($tableGateway);
                    return $table;
                },
                'TakeoutDishTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TakeoutDish());
                    return new TableGateway('takeoutdish', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
    
}
