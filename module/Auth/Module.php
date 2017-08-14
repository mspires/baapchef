<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;


use Zend\Authentication\Adapter\DbTable as DbAuthAdapter;
use Zend\Session\Container;
use Auth\Model\User;
use Auth\Model\UserRole;
use Auth\Model\PermissionTable;
use Auth\Model\ResourceTable;
use Auth\Model\RolePermissionTable;
use Zend\Authentication\AuthenticationService;
use Auth\Model\Role;
use Auth\Utility\Acl;

class Module implements AutoloaderProviderInterface
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
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'boforeDispatch'
        ), 100);        
    }
    
    function boforeDispatch(MvcEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $target = $event->getTarget();

        $homeList = array(
            'Admin\Controller\Home-index',
            'Agent\Controller\Home-index',
            'Restaurant\Controller\Home-index',
            'Customer\Controller\Home-index',
        );
        
        $whiteList = array(
            'Auth\Controller\Auth-signup',
            'Auth\Controller\Auth-login',
            'Auth\Controller\Auth-logout',
            'Login\Controller\Login-index',
            'Login\Controller\Login-logout'            
        );

        $requestUri = $request->getRequestUri();
        $controller = $event->getRouteMatch()->getParam('controller');

        $action = $event->getRouteMatch()->getParam('action');

        $requestedResourse = $controller . "-" . $action;

        $session = new Container('User');
        
        if ( in_array($requestedResourse, $homeList) )
        {
            $response->sendHeaders();
        }
        else
        {
            if ($session->offsetExists('email')) {
                                
                if ($requestedResourse == 'Auth\Controller\Auth-index' || in_array($requestedResourse, $whiteList)) {
                    $url = '/';
                    $response->setHeaders($response->getHeaders()
                        ->addHeaderLine('Location', $url));
                    $response->setStatusCode(302);
                } else {
    
                    $serviceManager = $event->getApplication()->getServiceManager();
                    $userRole = $session->offsetGet('roleName');
    
                    $acl = $serviceManager->get('Acl');
                    $acl->initAcl();
    
                    $status = $acl->isAccessAllowed($userRole, $controller, $action);
//                     if (! $status) {
//                         die('Permission denied');
//                     }
                }
            } else {
    
//                 if ($controller != 'Application\Controller\Index' && ! in_array($requestedResourse, $whiteList)) {
                                        
//                     $url = '/login';
//                     $response->setHeaders($response->getHeaders()
//                         ->addHeaderLine('Location', $url));
//                     $response->setStatusCode(302);
//                 }
                $response->sendHeaders();
            }
        }
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                    'AuthService' => function ($serviceManager)
                    {
                        $adapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                        $dbAuthAdapter = new DbAuthAdapter($adapter, 'users', 'email', 'password');
                        $auth = new AuthenticationService();
                        $auth->setAdapter($dbAuthAdapter);
                        return $auth;
                    },
                    'Acl' => function ($serviceManager)
                    {
                        return new Acl();
                    },
                    'UserTable' => function ($serviceManager)
                    {
                        return new User($serviceManager->get('Zend\Db\Adapter\Adapter'));
                    },
                    'RoleTable' => function ($serviceManager)
                    {
                        return new Role($serviceManager->get('Zend\Db\Adapter\Adapter'));
                    },
                    'UserRoleTable' => function ($serviceManager)
                    {
                        return new UserRole($serviceManager->get('Zend\Db\Adapter\Adapter'));
                    },
                    'PermissionTable' => function ($serviceManager)
                    {
                        return new PermissionTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                    },
                    'ResourceTable' => function ($serviceManager)
                    {
                        return new ResourceTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                    },
                    'RolePermissionTable' => function ($serviceManager)
                    {
                        return new RolePermissionTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                    }
            )
        );
    }
}
