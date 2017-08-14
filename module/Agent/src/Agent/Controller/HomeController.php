<?php
namespace Agent\Controller;

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
    protected $agentTable;
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
        if (!$this->session->offsetExists('agentID')) {
        
            return new ViewModel();
        }
        else 
        {
            if ($this->session->offsetExists('roleId')) {
                $gid = $this->session->agentID;
                $agent = $this->getAgentTable()->getAgent($gid);
                
                $view = new ViewModel(array(
                    'agent' => $agent,
                    'role' => $this->session->roleName,
                    'users' => $this->getUserTable()->getUseres(User::USER_AGENT,$gid),
                    'events' => $this->getEventTable()->getEvents(),
                    'alerts' => $this->getAlertTable()->getAlerts(),
                    'requests' => $this->getRequestTable()->getRequests(),
                
                ));
                $view->setTemplate('aghomeme');
                
                return $view;
            }
            else 
            {
                $gid = $this->session->agentID;
                $agent = $this->getAgentTable()->getAgent($gid);
                
                $view = new ViewModel(array(
                    'agent' => $agent,
                    'users' => $this->getUserTable()->getUseres(User::USER_AGENT,$gid),
                    'events' => $this->getEventTable()->getEvents(),
                    'alerts' => $this->getAlertTable()->getAlerts(),
                    'requests' => $this->getRequestTable()->getRequests(),
                ));
                $view->setTemplate('agpinin');
                
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
    
    public function getAgentTable()
    {
        if (!$this->agentTable) {
            $sm = $this->getServiceLocator();
            $this->agentTable = $sm->get('Agent\Model\AgentTable');
        }
        return $this->agentTable;
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