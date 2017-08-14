<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Order\Model\Order;
use Order\Model\OrderItem;
use Order\Form\OrderForm;
use Zend\Session\Container;
use Zend\Log\Logger;
use MyLib\Controller\AppController;
use Zend\Json\Json;

class OrderController extends AbstractActionController
{
    protected  $restaurantTable;
    protected $addressTable;
    protected $orderTable;
    protected $orderItemTable;
    
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
        if ($this->session->offsetExists('restaurantID')) {
        
            $rid = $this->session->restaurantID;
        
            $headScript = $this->getServiceLocator()->get('viewhelpermanager')->get('headScript');        
            $headScript->appendFile('/js/disable-input.js');        
            
            return new ViewModel(array(
                'orders' => $this->getOrderTable()->getOrderesTo($rid),
            ));
        }
        else 
        {
            return $this->redirect()->toRoute('restauranthome');
        }
    }

    public function reservationAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
    
            return new ViewModel();
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        }
    }
        
    public function monitorAction()
    {
        if ($this->session->offsetExists('restaurantID')) {


            $request = $this->getRequest();
            
            if ($request->isPost()) {
            
                $data = $request->getPost();
            
                $view = $this->acceptableViewModelSelector($this->acceptCriteria);
                Json::$useBuiltinEncoderDecoder = true;
            
                $rid = $this->session->restaurantID;
            
                //$orders = $this->getOrderTable()->getOrderesToNow($rid);
                $orders = $this->getOrderTable()->getOrderesTo($rid)->toArray();
                foreach ($orders as $key => $value)
                {
                    $orders[$key]['orderitems'] = $this->getOrderItemTable()->getOrderItems($value['id'])->toArray();
                }
            
                $view->setVariables(array(
                    'orders' => $orders,
                ));
                $view->setTerminal(true);
            
                return $view;
            }
            else
            {
                $view = $this->acceptableViewModelSelector($this->acceptCriteria);
                Json::$useBuiltinEncoderDecoder = true;
            
                $rid = $this->session->restaurantID;
            
                //$orders = $this->getOrderTable()->getOrderesToNow($rid);
                $orders = $this->getOrderTable()->getOrderesTo($rid)->toArray();
                foreach ($orders as $key => $value)
                {
                    $orders[$key]['orderitems'] = $this->getOrderItemTable()->getOrderItems($value['id'])->toArray();
                }
            
                $view->setVariables(array(
                    'orders' => $orders,
                ));
                $view->setTerminal(true);
            
                return $view;
            }            
        }
        else 
        {
            return $this->redirect()->toRoute('restauranthome');
        }
    }
    
    public function listAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
        
            $request = $this->getRequest();
            
            if ($request->isPost()) {
        
                $data = $request->getPost();
        
                $view = $this->acceptableViewModelSelector($this->acceptCriteria);
                Json::$useBuiltinEncoderDecoder = true;
                
                $rid = $this->session->restaurantID;
                
                //$orders = $this->getOrderTable()->getOrderesToNow($rid);
                $orders = $this->getOrderTable()->getOrderesTo($rid)->toArray();
                foreach ($orders as $key => $value)
                {
                    $orders[$key]['orderitems'] = $this->getOrderItemTable()->getOrderItems($value['id'])->toArray();
                }
            
                $view->setVariables(array(
                    'orders' => $orders,
                ));            
                $view->setTerminal(true);
                
                return $view;
            }
            else
            {
                $view = $this->acceptableViewModelSelector($this->acceptCriteria);
                Json::$useBuiltinEncoderDecoder = true;
                
                $rid = $this->session->restaurantID;
                
                //$orders = $this->getOrderTable()->getOrderesToNow($rid);
                $orders = $this->getOrderTable()->getOrderesTo($rid)->toArray();
                foreach ($orders as $key => $value)
                {
                    $orders[$key]['orderitems'] = $this->getOrderItemTable()->getOrderItems($value['id'])->toArray();
                }
            
                $view->setVariables(array(
                    'orders' => $orders,
                ));            
                $view->setTerminal(true);
                
                return $view;
            }
        }
        else 
        {
            return $this->redirect()->toRoute('restauranthome');
        }
    }
    
    public function ordernowAction()
    {
        $request = $this->getRequest();
    
        if ($request->isPost()) {
    
            $data = json_decode($this->getRequest()->getContent(),true);
            //$data = $request->getPost();
            
            $view = $this->acceptableViewModelSelector($this->acceptCriteria);
            Json::$useBuiltinEncoderDecoder = true;
    
            $id = $this->session->restaurantID;
    
            $customerid = 0;
            
            if($data['customer'] != null) {
                $customerid = $data['customer']['id'];
            }
            
            if($data['orderid'] != null)
            {
                $orderid = $data['orderid'];
                
                $this->getOrderItemTable()->deleteOrderItems($orderid);
            }
            else
            {
                $order = new Order();
                $order->rid = $id;
                $order->cid = $customerid;
                $order->ordertype = 1;
                $order->rstate = 1;
                $order->cstate = 1;
                $order->reward = 0;
                
                $orderid = $this->getOrderTable()->saveOrder($order);
            }
            
            foreach ($data['orderItems'] as $item)
            {
                $orderitem = new OrderItem();
                
                $orderitem->orderid = $orderid;
                $orderitem->dishid = $item['id'];
                $orderitem->qty = 1;
                $orderitem->note = '';
                $orderitem->state = 0;
                
                $this->getOrderItemTable()->saveOrderItem($orderitem);
            }
            $data['order'] = $this->getOrderTable()->getOrder($orderid);//->toArray();
            
            $view->setVariables(array(
                'data' => $data,
            ));
    
            return $view;
        }
    }

    public function cancelAction()
    {
        $request = $this->getRequest();
    
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
    
        Json::$useBuiltinEncoderDecoder = true;
    
        $id = $this->session->restaurantID;
    
        if ($request->isPost()) {
    
            $data = json_decode($this->getRequest()->getContent(),true);
            //$data = $request->getPost();
    
            $orderid = $data['id'];
    
            //$state = ROrderState::toOrdinal(ROrderState::RORDERSTATE_COOKING);
            $this->getOrderTable()->changeROrderState($orderid, -1);
    
            $view->setVariables(array(
                'order' => $this->getOrderTable()->getOrder($orderid),
                'orderitems' => $this->getOrderItemTable()->getOrderItems($orderid),
            ));
        }
    
        return $view;
    }
   
    public function incookAction()
    {
        $request = $this->getRequest();
    
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
    
        Json::$useBuiltinEncoderDecoder = true;
    
        $id = $this->session->restaurantID;
    
        if ($request->isPost()) {
    
            $data = json_decode($this->getRequest()->getContent(),true);
            //$data = $request->getPost();
    
            $orderid = $data['id'];
    
            //ROrderState::toOrdinal(ROrderState::RORDERSTATE_COOKING)
            $this->getOrderTable()->changeROrderState($orderid, 2);
    
            $view->setVariables(array(
                'order' => $this->getOrderTable()->getOrder($orderid),
                'orderitems' => $this->getOrderItemTable()->getOrderItems($orderid),
            ));
        }
    
        return $view;
    }

    public function outAction()
    {
        $request = $this->getRequest();
    
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
    
        Json::$useBuiltinEncoderDecoder = true;
    
        $id = $this->session->restaurantID;
    
        if ($request->isPost()) {
    
            $data = json_decode($this->getRequest()->getContent(),true);
            //$data = $request->getPost();
    
            $orderid = $data['orderid'];
    
            //$state = ROrderState::toOrdinal(ROrderState::RORDERSTATE_COOKING);
            $this->getOrderTable()->changeROrderState($orderid, 3);
    
            $view->setVariables(array(
                'order' => $this->getOrderTable()->getOrder($orderid),
                'orderitems' => $this->getOrderItemTable()->getOrderItems($orderid),
            ));
        }
    
        return $view;
    }
    
    public function receiptAction()
    {
        $request = $this->getRequest();
    
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
    
        Json::$useBuiltinEncoderDecoder = true;
    
        $rid = $this->session->restaurantID;
    
        if ($request->isPost()) {
    
            $data = json_decode($this->getRequest()->getContent(),true);
            //$data = $request->getPost();
    
            $orderid = $data['id'];    
        }
        else 
        {
            $orderid = (int) $this->params()->fromRoute('id', 0);
        }
    
        $view->setVariables(array(
            'restaurant' => $this->getRestaurantTable()->getRestaurant($rid),
            'addresses' => $this->getAddressTable()->getAddresses('restaurant', $rid),
            'order' => $this->getOrderTable()->getOrder($orderid),
            'orderitems' => $this->getOrderItemTable()->getOrderItems($orderid),
        ));
        
        return $view;
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('restorder', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $order = $this->getOrderTable()->getOrder($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('restorder', array(
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
                return $this->redirect()->toRoute('restorder');
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
            return $this->redirect()->toRoute('restorder');
        }
    
        $this->getOrderTable()->deleteOrder($id);
        return $this->redirect()->toRoute('restorder');
    }
    
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurantTable;
    }
    
    public function getAddressTable()
    {
        if (!$this->addressTable) {
            $sm = $this->getServiceLocator();
            $this->addressTable = $sm->get('Restaurant\Model\AddressTable');
        }
        return $this->addressTable;
    }
    
    public function getOrderItemTable()
    {
        if (!$this->orderItemTable) {
            $sm = $this->getServiceLocator();
            $this->orderItemTable = $sm->get('Order\Model\OrderItemTable');
        }
        return $this->orderItemTable;
    }
    
    public function getOrderTable()
    {
        if (!$this->orderTable) {
            $sm = $this->getServiceLocator();
            $this->orderTable = $sm->get('Order\Model\OrderTable');
        }
        return $this->orderTable;
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