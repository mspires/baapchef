<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

use Zend\Session\Container;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Application\Model\SessionHandler;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        //$this->bootstrapSession($e);
        $this->initDbSession($e);
    }
    
    private function initDbSession( MvcEvent $e )
    {
        $serviceManager     = $e->getApplication()->getServiceManager();
        
        /*
        // grab the config array
        $config             = $serviceManager->get('config');
        
        $dbAdapter          = $serviceManager->get('Zend\Db\Adapter\Adapter');
        
        $tableGateway   = new TableGateway('session', $dbAdapter, null, null);
        $saveHandler   = new SessionHandler( $tableGateway );
        
        $sessionConfig = new \Zend\Session\Config\SessionConfig();
        $sessionConfig->setOptions($config['session']);
        
        // pass the saveHandler to the sessionManager and start the session
        $sessionManager = new \Zend\Session\SessionManager( $sessionConfig , NULL, $saveHandler );
        $sessionManager->start();
        
        Container::setDefaultManager($sessionManager);
        */
        
        $serviceManager->get('Application\Model\SessionHandler');
    }
    
    public function bootstrapSession($e)
    {
//         $session = $e->getApplication()
//         ->getServiceManager()
//         ->get('Zend\Session\SessionManager');
//         $session->start();
    
//         $container = new Container('initialized');
//         if (!isset($container->init)) {
//             $serviceManager = $e->getApplication()->getServiceManager();
//             $request        = $serviceManager->get('Request');
    
//             $session->regenerateId(true);
//             $container->init          = 1;
//             $container->remoteAddr    = $request->getServer()->get('REMOTE_ADDR');
//             $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
    
//             $config = $serviceManager->get('Config');
//             if (!isset($config['session'])) {
//                 return;
//             }
    
//             $sessionConfig = $config['session'];
//             if (isset($sessionConfig['validators'])) {
//                 $chain   = $session->getValidatorChain();
    
//                 foreach ($sessionConfig['validators'] as $validator) {
//                     switch ($validator) {
//                         case 'Zend\Session\Validator\HttpUserAgent':
//                             $validator = new $validator($container->httpUserAgent);
//                             break;
//                         case 'Zend\Session\Validator\RemoteAddr':
//                             $validator  = new $validator($container->remoteAddr);
//                             break;
//                         default:
//                             $validator = new $validator();
//                     }
    
//                     $chain->attach('session.validate', array($validator, 'isValid'));
//                 }
//             }
//         }
    }
    
    public function getServiceConfig()
    {
         return array(
             'factories' => array(
                 'Application\Model\SessionHandler' =>  function($sm) {
                     $tableGateway = $sm->get('SessionHandlerGateway');
                     $table = new SessionHandler($tableGateway);
                     return $table;
                 },
                 'SessionHandlerGateway' => function ($sm) {
                     
                    $config = $sm->get('config');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    
                    $tableGateway   = new TableGateway('session', $dbAdapter, null, null);
                    $saveHandler   = new SessionHandler( $tableGateway );
                    
                    $sessionConfig = new \Zend\Session\Config\SessionConfig();
                    $sessionConfig->setOptions($config['session']);
                    
                    $sessionManager = new \Zend\Session\SessionManager( $sessionConfig , NULL, $saveHandler );
                    $sessionManager->start();
                    
                    Container::setDefaultManager($sessionManager);
                    
                    return $saveHandler;
                 },
                 'mail.transport' => function ($sm) {
                     $config = $sm->get('Config');
                     $transport = new Smtp();
                     $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
                 
                     return $transport;
                 },
                  
//                 'Zend\Session\SessionManager' => function ($sm) {
//                     $config = $sm->get('config');
//                     if (isset($config['session'])) {
//                         $session = $config['session'];
    
//                         $sessionConfig = null;
//                         if (isset($session['config'])) {
//                             $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
//                             $options = isset($session['config']['options']) ? $session['config']['options'] : array();
//                             $sessionConfig = new $class();
//                             $sessionConfig->setOptions($options);
//                         }
    
//                         $sessionStorage = null;
//                         if (isset($session['storage'])) {
//                             $class = $session['storage'];
//                             $sessionStorage = new $class();
//                         }
    
//                         $sessionSaveHandler = null;
//                         if (isset($session['save_handler'])) {
//                             // class should be fetched from service manager since it will require constructor arguments
//                             $sessionSaveHandler = $sm->get($session['save_handler']);
//                         }
    
//                         $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
//                     } else {
//                         $sessionManager = new SessionManager();
//                     }
//                     Container::setDefaultManager($sessionManager);
//                     return $sessionManager;
//                 },
             ),
         );
    }
    

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
