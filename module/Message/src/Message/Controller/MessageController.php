<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Message for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Message\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Message\Form\ReqForm;
use Message\Form\Filter\ReqFilter;
use Message\Form\RespForm;
use Message\Form\Filter\RespFilter;
use Message\Model\Request;
use Message\Model\Response;

class MessageController extends AbstractActionController
{
    private $requestTable;
    private $responseTable;
    
    public function indexAction()
    {
        $form = new ReqForm();
        $form->setInputFilter(new ReqFilter());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                print_r($form->getData());
                exit;
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
                
                return $this->redirect()->toRoute('message',array('action'=>'list'));
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
                
                return $this->redirect()->toRoute('message',array('action'=>'list'));
                exit;
            }
        }
        return new ViewModel(array('form' => $form));
    }
    
    public function listAction()
    {
        return new ViewModel(array(
            'messages' => $this->getRequestTable()->fetchAll(),
        ));
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