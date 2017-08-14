<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\User;
use Event\Model\Event;
use Alert\Model\Alert;
use Message\Model\Request;

use Zend\Session\Container;
use Zend\Log\Logger;
use Auth\Model\Role;

class HomeController extends AbstractActionController
{
    protected  $restaurantTable;
    protected $userTable;
    protected $eventTable;
    protected $alertTable;
    protected $requestTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if (!$this->session->offsetExists('restaurantID')) {
        
            return new ViewModel();
        }
        else 
        {
            if ($this->session->offsetExists('roleId')) {
                
                $rid = $this->session->restaurantID;
                $roleId = $this->session->roleId;
                
                if($roleId == Role::ROLE_ADMIN || $roleId == Role::ROLE_MANAGER)
                {
                    $view = new ViewModel(array(
                        'restaurant' => $this->getRestaurantTable()->getRestaurant($rid),
                        'users' => $this->getUserTable()->getUseres(User::USER_RESTAURANT,$rid),
                        'events' => $this->getEventTable()->getEvents(),
                        'alerts' => $this->getAlertTable()->getAlerts(),
                        'requests' => $this->getRequestTable()->getRequests(),
                    ));
                    $view->setTemplate('resthomeme');
                    return $view;
                }
                else
                {
                    $view = new ViewModel(array(
                        'restaurant' => $this->getRestaurantTable()->getRestaurant($rid),
                        'users' => $this->getUserTable()->getUseres(User::USER_RESTAURANT,$rid),
                        'events' => $this->getEventTable()->getEvents(),
                        'alerts' => $this->getAlertTable()->getAlerts(),
                        'requests' => $this->getRequestTable()->getRequests(),
                    ));
                    $view->setTemplate('resthomeme2');
                    return $view;
                }
                /*
                else if($roleId == Role::ROLE_STAFF)
                {
                    return $this->redirect()->toRoute('restdish', array(
                        'action' => 'menu'
                    ));
                }
                else if($roleId == Role::ROLE_MEMBER)
                {
                    return $this->redirect()->toRoute('restorder', array(
                        'action' => 'monitor'
                    ));
                }
                */
            }
            else 
            {
                
                $rid = $this->session->restaurantID;
                
                $view = new ViewModel(array(
                    'restaurant' => $this->getRestaurantTable()->getRestaurant($rid),
                    'users' => $this->getUserTable()->getUseres(User::USER_RESTAURANT,$rid),
                    'events' => $this->getEventTable()->getEvents(),
                    'alerts' => $this->getAlertTable()->getAlerts(),
                    'requests' => $this->getRequestTable()->getRequests(),
                ));
                $view->setTemplate('restpinin');
                
                return $view;
            }
        }
    }
    
    public function getEventTable()
    {
        if (!$this->eventTable) {
            $sm = $this->getServiceLocator();
            $this->eventTable = $sm->get('Event\Model\EventTable');
        }
        return $this->eventTable;
    }
    
    public function getAlertTable()
    {
        if (!$this->alertTable) {
            $sm = $this->getServiceLocator();
            $this->alertTable = $sm->get('Alert\Model\AlertTable');
        }
        return $this->alertTable;
    }
    
    public function getRequestTable()
    {
        if (!$this->requestTable) {
            $sm = $this->getServiceLocator();
            $this->requestTable = $sm->get('Message\Model\RequestTable');
        }
        return $this->requestTable;
    }
    
    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
    
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurantTable;
    }
    
    public function getLogger()
    {
        if(!$this->logger)
        {
            $this->logger = $this->getServiceLocator()->get('Zend\Log');
        }
        return $this->logger;
    }
}