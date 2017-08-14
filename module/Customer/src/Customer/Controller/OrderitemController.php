<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Order for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Order\Model\OrderItem;
use Order\Form\OrderItemForm;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Zend\Session\Container;
use Zend\Log\Logger;

class OrderitemController extends AbstractActionController
{
    protected $orderItemTable;
    protected $dishTable;
    
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
        if ($this->session->offsetExists('customerID')) {
        
            $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        
            Json::$useBuiltinEncoderDecoder = true;
        
            $id = $this->session->customerID;
        
            return $view->setVariables(array(
                'orderitems' => $this->getOrderItemTable()->getOrderItems(3),
            ));
        }
        else
        {
            return $this->redirect()->toRoute('customerhome');
        }         
    }
    
    public function addAction()
    {
        $form = new OrderItemForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $orderitem = new OrderItem();
            $form->setInputFilter($orderitem->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $orderitem->exchangeArray($form->getData());
                $this->getOrderItemTable()->saveOrder($orderitem);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('orderitem');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('orderitem', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $orderitem = $this->getOrderItemTable()->getOrderItem($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('orderitem', array(
                'action' => 'index'
            ));
        }
    
        $form  = new OrderItemForm();
        $form->bind($orderitem);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($orderitem->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getOrderItemTable()->saveOrderItem($orderitem);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('orderitem');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
            'dish' => $this->getDishTable()->getDish($orderitem->dishid)
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('orderitem');
        }
    
        $this->getOrderTable()->deleteOrder($id);
        return $this->redirect()->toRoute('orderitem');
    }
        
    public function getOrderItemTable()
    {
        if (!$this->orderItemTable) {
            $sm = $this->getServiceLocator();
            $this->orderItemTable = $sm->get('Order\Model\OrderItemTable');
        }
        return $this->orderItemTable;
    }
    
    public function getDishTable()
    {
        if (!$this->dishTable) {
            $sm = $this->getServiceLocator();
            $this->dishTable = $sm->get('Dish\Model\DishTable');
        }
        return $this->dishTable;
    }
}
