<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Order\Model\Order;
use Order\Model\OrderItem;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Zend\Session\Container;
use Zend\Log\Logger;

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
        if ($this->session->offsetExists('customerID')) {
        
            $view = $this->acceptableViewModelSelector($this->acceptCriteria);
            
            Json::$useBuiltinEncoderDecoder = true;
            
            $id = $this->session->customerID;        
            return $view->setVariables(array(
                'orders' => $this->getOrderTable()->getOrderesFrom($id),
            ));     
        }
        else
        {
            
            return $this->redirect()->toRoute('customerhome');
        }
    }
    /*
    public function ordernowAction()
    {
        return new ViewModel(array(
            'orders' => $this->getOrderTable()->getOrderesFrom(1),
        ));
    }
    */
    public function ordernowAction()
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
            $rid=$data['rid'];
            $cid=$data['cid'];
            if($rid == 0)
            {
                $data = json_decode($this->getRequest()->getContent(),true);
                $rid = $data["rid"];
                $cid = $data["cid"];
            }
    
            $order = new Order();
            $order->rid = $rid;
            $order->cid = $cid;
            $order->ordertype = 1;
            $order->rstate = 1;
            $order->cstate = 1;
            $order->reward = 0;

            $orderid = $this->getOrderTable()->saveOrder($order);

            foreach ($data['orderitems'] as $item)
            {
                $orderitem = new OrderItem();
    
                $orderitem->orderid = $orderid;
                $orderitem->dishid = $item['id'];
                $orderitem->qty = 1;
                $orderitem->note = $item['note'];
                $orderitem->state = 0;
    
                $this->getOrderItemTable()->saveOrderItem($orderitem);
            }
            $data['order'] = $this->getOrderTable()->getOrder($orderid);
    
            $view->setVariables(array(
                'data' => $data,
            ));
    
            return $view;
        }
    }
    
    public function receiptAction()
    {
        $request = $this->getRequest();
    
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
    
        Json::$useBuiltinEncoderDecoder = true;
    
        if ($request->isPost()) {
    
            $data = json_decode($this->getRequest()->getContent(),true);
            //$data = $request->getPost();

            $orderid = $data['id'];
            $rid = $data['rid'];
        }
        else
        {
            $orderid = (int) $this->params()->fromRoute('id', 0);
            $rid = (int) $this->params()->fromRoute('rid', 0);
        }
        
        return $view->setVariables(array(
            'restaurant' => $this->getRestaurantTable()->getRestaurant($rid),
            'addresses' => $this->getAddressTable()->getAddresses('restaurant', $rid),
            'order' => $this->getOrderTable()->getOrder($orderid),
            'orderitems' => $this->getOrderItemTable()->getOrderItems($orderid),
        ));

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