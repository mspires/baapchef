<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Message for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Message\Form\ReqForm;
use Message\Form\Filter\ReqFilter;
use Message\Form\RespForm;
use Message\Form\Filter\RespFilter;
use Message\Model\Request;
use Message\Model\Response;

use Zend\Session\Container;
use Zend\Log\Logger;

use Admin\Model\SendEmail;

class MessageController extends AbstractActionController
{
    private $requestTable;
    private $responseTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        return new ViewModel(array(
            'messages' => $this->getRequestTable()->fetchAll(),
        ));
    }
    
    public function addAction()
    {
        $form = new ReqForm();
        $form->setInputFilter(new ReqFilter());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $message = new Request();
                $message->exchangeArray($form->getData());
                $this->getRequestTable()->saveRequest($message);
                
                $email = new SendEmail($this);
                $email->sendrequest($message);
                
                return $this->redirect()->toRoute('adminmessage',array('action'=>'list'));
            }
        }
        return new ViewModel(array('form' => $form));
    }
    public function requestAction()
    {
        $form = new ReqForm();
        $form->setInputFilter(new ReqFilter());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $message = new Request();
                $message->exchangeArray($form->getData());
                $this->getRequestTable()->saveRequest($message);
                
                $email = new SendEmail($this);
                $email->sendrequest($message);
                
                return $this->redirect()->toRoute('adminmessage',array('action'=>'list'));
            }
        }
        return new ViewModel(array('form' => $form));
    }
    
    public function responseAction()
    {
        //$id = (int)$id;
        $form = new RespForm();
        $form->setInputFilter(new RespFilter());
        $form->get('reqid')->setValue(1);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $message = new Response();
                $message->exchangeArray($form->getData());
                $this->getResponseTable()->saveResponse($message);
                
                $email = new SendEmail($this);
                $email->sendresponse($message);
                
                return $this->redirect()->toRoute('adminmessage',array('action'=>'list'));
            }
        }
        return new ViewModel(array('form' => $form));
    }

    public function getRequestTable()
    {
        if (!$this->requestTable) {
            $sm = $this->getServiceLocator();
            $this->requestTable = $sm->get('Message\Model\RequestTable');
        }
        return $this->requestTable;
    }
    
    public function getResponseTable()
    {
        if (!$this->responseTable) {
            $sm = $this->getServiceLocator();
            $this->responseTable = $sm->get('Message\Model\ResponseTable');
        }
        return $this->responseTable;
    }
}