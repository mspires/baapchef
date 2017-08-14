<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Agent for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Agent;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\Session\Container;

use Agent\Model\Agent;
use Agent\Model\AgentTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ModuleManager\ModuleManager;

class Module implements AutoloaderProviderInterface
{
    
    public function init(ModuleManager $mm)
    {
        $mm->getEventManager()->getSharedManager()->attach(__NAMESPACE__,
            'dispatch', function($e) {
                $e->getTarget()->layout('agent/layout');
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
         $controller->layout()->publickey = sha1('agent');
    }
    
    public function getServiceConfig() {
    
        return array(
            'factories' => array(
                'Agent\Model\AgentTable' =>  function($sm) {
                    $tableGateway = $sm->get('AgentTableGateway');
                    $table = new AgentTable($tableGateway);
                    return $table;
                },
                'AgentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Agent());
                    return new TableGateway('agent', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }    
}
