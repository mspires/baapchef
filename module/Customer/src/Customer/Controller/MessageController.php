<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Message\Model\Request;
use Event\Model\Event;
use Alert\Model\Alert;

use Zend\Session\Container;
use Zend\Log\Logger;


class MessageController extends AbstractActionController
{
    private $requestTable;
    private $eventTable;
    private $alertTable;

    protected $session;
    protected $logger;

    private $acceptCriteria = array(
        'Zend\View\Model\ViewModel' => array(
            'text/html',
        ),
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        ));
    
    public function __construct()
    {
        $this->session = new Container('User');
    }

    public function indexAction()
    {
        return new ViewModel(array(
            'messages' => $this->getRequestTable()->fetchAll(),
            'events' => $this->getEventTable()->fetchAll(),
            'alerts' => $this->getAlertTable()->fetchAll(),
        ));
    }
    
    public function msgAction()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeaders($headers);
        $request = $this->getRequest();
    
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        Json::$useBuiltinEncoderDecoder = true;
    
        if ($request->isPost()) {
            $data = $request->getPost();
            $cid=$data['cid'];
            if($cid == 0)
            {
                $data = json_decode($this->getRequest()->getContent(),true);
                $cid = $data["cid"];
            }   
            
            $view->setVariables(array(
                'messages' => $this->getRequestTable()->fetchAll(),
                'events' => $this->getEventTable()->fetchAll(),
                'alerts' => $this->getAlertTable()->fetchAll(),
                ));
        }
        else 
        {
        
            $view->setVariables(array(
                'messages' => $this->getRequestTable()->fetchAll(),
                'events' => $this->getEventTable()->fetchAll(),
                'alerts' => $this->getAlertTable()->fetchAll(),
                ));  
        }
        
        return $view;
    }
    
    public function getRequestTable()
    {
        if (!$this->requestTable) {
            $sm = $this->getServiceLocator();
            $this->requestTable = $sm->get('Message\Model\RequestTable');
        }
        return $this->requestTable;
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
}