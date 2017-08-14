<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Restaurant\Model\Restaurant;
use Restaurant\Model\Address;

use Zend\Session\Container;
use Zend\Log\Logger;
/**
 * RestaurantController
 *
 * @author
 *
 * @version
 *
 */
class RestaurantController extends AbstractActionController
{
    protected $restaurantTable;
    protected $addressTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    
    }

    public function indexAction()
    {
        $method = $this->params()->fromQuery('method', 'get');
    
    
        if ($this->session->offsetExists('customerID')) {
    
            $id = $this->session->customerID;
    
            return new ViewModel(array(
                'restaurants' => $this->getRestaurantTable()->getRestaurants($id),
            ));
        }
        else
        {
            return $this->redirect()->toRoute('customerhome');
        }
    }
        
    public function infoAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        
        if ($this->session->offsetExists('customerID')) {
        
            $addresses = $this->getAddressTable()->getAddresses('restaurant', $id);
            
            return new ViewModel(array(
                'restaurant' => $this->getRestaurantTable()->getRestaurant($id),
                'address' => $this->getAddressTable()->getAddressByType('restaurant', $id),
            )); 
        }
        else
        {
            return $this->redirect()->toRoute('customerhome');
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
    
    public function getAddressTable()
    {
        if (!$this->addressTable) {
            $sm = $this->getServiceLocator();
            $this->addressTable = $sm->get('Restaurant\Model\AddressTable');
        }
        return $this->addressTable;
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