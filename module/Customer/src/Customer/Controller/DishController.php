<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\Dish;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Zend\Session\Container;
use Zend\Log\Logger;

class DishController extends AbstractActionController
{
    protected $dishTable;
    protected $restaurantTable;
    protected $dishgroupTable;
    
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
            
            $rid = (int) $this->params()->fromRoute('id', 0);
            $groupid = 0;
            if($rid != 0)
            {
                $rid = (int) $this->params()->fromRoute('id', 0);
                $id = $this->session->customerID;
                $groupid = $this->params()->fromQuery('$dishgroup',0);
                
            }
            else 
            {
                $data = json_decode($this->getRequest()->getContent(),true);
                $rid = $data["rid"];
            }
            
            
            $current_restaurant = null;
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
        
            if($rid != 0)
            {
                return $view->setVariables(array(
                    'dishes' => $this->getDishTable()->getDishesByGroup($rid, $groupid),
                    'restaurants' => $this->getRestaurantTable()->getRestaurants($id),
                    'dishgroups' => $this->getDishgroupTable()->getDishgroups($rid),
                    'current_restaurant' => $current_restaurant,
                ));
            }
            else 
            {
                return $this->redirect()->toRoute('customerhome');
            }
        }
        else
        {      
            return $this->redirect()->toRoute('customerhome');
        }               
    }
    
    public function menuAction()
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
            if($rid == 0)
            {
                $data = json_decode($this->getRequest()->getContent(),true);
                $rid = $data["rid"];
            }
            
            $view->setVariables(array(
                'restaurant' => $this->getRestaurantTable()->getRestaurant($rid),
                'dishes' => $this->getDishTable()->getDishes($rid),
                'dishgroups' => $this->getDishgroupTable()->getDishgroups($rid),
            ));
        }
        else
        {
            $rid = $this->params()->fromQuery('rid',0);
            
            if($rid != 0)
            {
                $view->setVariables(array(
                    'restaurant' => $this->getRestaurantTable()->getRestaurant($rid),
                    'dishes' => $this->getDishTable()->getDishes($rid),
                    'dishgroups' => $this->getDishgroupTable()->getDishgroups($rid),
                ));
            }
        }
    
        return $view;
    }
    
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurantTable;
    }
    
    public function getDishgroupTable()
    {
        if (!$this->dishgroupTable) {
            $sm = $this->getServiceLocator();
            $this->dishgroupTable = $sm->get('Dish\Model\DishgroupTable');
        }
        return $this->dishgroupTable;
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