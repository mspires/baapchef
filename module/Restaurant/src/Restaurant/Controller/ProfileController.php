<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Form\LoginForm;
use Auth\Form\Filter\LoginFilter;
use Zend\Session\Container;

class ProfileController extends AbstractActionController
{
    protected  $restaurantTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('restaurantID')) 
        {
            $id = $this->session->restaurantID;
            $restaurant = $this->getRestaurantTable()->getRestaurant($id);
    
            $view = new ViewModel(array(
                'restaurant' => $restaurant,
    
            ));
            
            return $view;
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        }
    }
    
    public function changeAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
    
            $id = $this->session->restaurantID;
            $restaurant = $this->getRestaurantTable()->getRestaurant($id);
    
            $view = new ViewModel(array(
                'restaurant' => $restaurant,
    
            ));
    
            return $view;
        }
    }
    
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurantTable;
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