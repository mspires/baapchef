<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Order for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Order\Model\OrderItem;
use Order\Form\OrderItemForm;
use Zend\Session\Container;
use Zend\Json\Json;
use Order\Model\ROrderStatus;

class OrderitemController extends AbstractActionController
{
    protected $orderTable;
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
        $id = (int) $this->params()->fromRoute('id', 0);
        
        return new ViewModel(array(
            'orderitems' => $this->getOrderItemTable()->getOrderItems($id),
        ));   
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

    public function viewAction()
    {
        $request = $this->getRequest();
    
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
    
        Json::$useBuiltinEncoderDecoder = true;
    
        $id = $this->session->restaurantID;
    
        if ($request->isPost()) {
    
            $data = $request->getPost();
    
            $orderid = $data['orderid'];
    
            $view->setVariables(array(
                'orderitems' => $this->getOrderItemTable()->getOrderItems($orderid),
            ));
        }
        else
        {
            $id = (int) $this->params()->fromRoute('id', 0);
  
            $view->setVariables(array(
                'orderitems' => $this->getOrderItemTable()->getOrderItems($id),
            ));
        }
    
        return $view;
    }

    public function getOrderTable()
    {
        if (!$this->orderTable) {
            $sm = $this->getServiceLocator();
            $this->orderTable = $sm->get('Order\Model\OrderTable');
        }
        return $this->orderTable;
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
    
    public function getLogger()
    {
        if(!$this->logger)
        {
            $this->logger = $this->getServiceLocator()->get('Zend\Log');
        }
        return $this->logger;
    }
}
