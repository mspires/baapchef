<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Order for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Order\Model\Order;
use Order\Form\OrderForm;
use Zend\Session\Container;
use Zend\Log\Logger;

class OrderController extends AbstractActionController
{
    protected $orderTable;
    protected $restaurantTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('adminID')) {
        
            $rid = (int) $this->params()->fromRoute('id', 0);
            $current_restaurant = null;
            $restaurants = $this->getRestaurantTable()->fetchAll();
            if($rid != 0)
            {
                $this->session->restaurantID = $rid;
                $current_restaurant = $this->getRestaurantTable()->getRestaurant($rid);
            }
            else if ($this->session->offsetExists('restaurantID'))
            {
                $rid = $this->session->restaurantID;
                $current_restaurant = $this->getRestaurantTable()->getRestaurant($rid);
            }
            
            return new ViewModel(array(
                'orders' => $this->getOrderTable()->getOrderesTo($rid),
                'restaurants' => $restaurants,
                'current_restaurant' => $current_restaurant,
            ));
           
        }   
        else
        {
            
            return $this->redirect()->toRoute('adminhome');
        }
    }
    
    public function addAction()
    {
        $form = new OrderForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $order = new Order();
            $form->setInputFilter($order->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $order->exchangeArray($form->getData());
                $this->getOrderTable()->saveOrder($order);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('adminorder');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminorder', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $order = $this->getOrderTable()->getOrder($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('adminorder', array(
                'action' => 'index'
            ));
        }
    
        $form  = new OrderForm();
        $form->bind($order);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($order->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getOrderTable()->saveOrder($order);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('order');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminorder');
        }
    
        $this->getOrderTable()->deleteOrder($id);
        return $this->redirect()->toRoute('adminorder');
    }
    
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurantTable;
    }
        
    public function getOrderTable()
    {
        if (!$this->orderTable) {
            $sm = $this->getServiceLocator();
            $this->orderTable = $sm->get('Order\Model\OrderTable');
        }
        return $this->orderTable;
    }
}
