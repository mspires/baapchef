<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Event\Model\Event;
use Alert\Model\Alert;
use Message\Model\Request;
use User\Model\User;

use Zend\Session\Container;
use Zend\Log\Logger;

class HomeController extends AbstractActionController
{
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

    /*
    public  function testAction()
    {
        $id=10000;
        
        $destination = sprintf('%s/%09d','./data/data',$id);
        $destination2 = sprintf('%s/%09d','./data/data/restaurant',$id);
        
        if (!file_exists($destination)) {
        
            mkdir($destination, 0777, true);
        }
        if (!file_exists($destination2)) {
        
            mkdir($destination2, 0777, true);
        }
        
        $logo = sprintf("./data/data/restaurant/%09d/%s.png", $id,'logo');
        copy("./data/data/restaurant/logo.png", $logo);
        
    }
    */
        
    public function indexAction()
    {
        if (!$this->session->offsetExists('adminID')) {
        
            return new ViewModel();
        }
        else 
        {
            $id = $this->session->adminID;
            
            $view = new ViewModel(array(
                'users' => $this->getUserTable()->getUseres(User::USER_ADMIN,$id),
                'events' => $this->getEventTable()->getEvents(),
                'alerts' => $this->getAlertTable()->getAlerts(),
                'requests' => $this->getRequestTable()->getRequests(),
            ));
            
            if (!$this->session->offsetExists('roleId')) {

                $view->setTemplate('pinin');                
            }
            else 
            {
                $view->setTemplate('homeme');
            }
            return $view;
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
    
    public function getLogger()
    {
        if(!$this->logger)
        {
            $this->logger = $this->getServiceLocator()->get('Zend\Log');
        }
        return $this->logger;
    }
}