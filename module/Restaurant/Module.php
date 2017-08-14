<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Restaurant for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Restaurant;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\Session\Container;

use Restaurant\Model\Restaurant;
use Restaurant\Model\RestaurantTable;
use Restaurant\Model\Address;
use Restaurant\Model\AddressTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;

class Module implements AutoloaderProviderInterface
{
    public function init(ModuleManager $mm)
    {
        $mm->getEventManager()->getSharedManager()->attach(__NAMESPACE__,
            'dispatch', function($e) {
                $e->getTarget()->layout('restaurant/layout');
            });
    }
    
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

        $eventManager->attach('dispatch', array($this, 'loadConfiguration' ));
        
    }
    
    public function loadConfiguration(MvcEvent $e)
    {
        $controller = $e->getTarget();
        
         $session = new Container('User');
        
         $controller->layout()->login = '0';
         
         if ($session->offsetExists('email')) {
             
             $controller->layout()->login = '1';
         }
         $controller->layout()->publickey = sha1('restaurant');
    }
    
    public function getServiceConfig() {
    
        return array(
            'factories' => array(
                'Restaurant\Model\RestaurantTable' =>  function($sm) {
                    $tableGateway = $sm->get('RestaurantTableGateway');
                    $table = new RestaurantTable($tableGateway);
                    return $table;
                },
                'RestaurantTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Restaurant());
                    return new TableGateway('restaurant', $dbAdapter, null, $resultSetPrototype);
                },
                'Restaurant\Model\AddressTable' =>  function($sm) {
                    $tableGateway = $sm->get('AddressTableGateway');
                    $table = new AddressTable($tableGateway);
                    return $table;
                },
                'AddressTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Address());
                    return new TableGateway('address', $dbAdapter, null, $resultSetPrototype);
                },                
            ),
        );
    }
}
