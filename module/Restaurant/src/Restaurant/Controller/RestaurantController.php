<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Restaurant for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Restaurant\Model\Restaurant;
use Restaurant\Form\RestaurantForm;
use Zend\Session\Container;

class RestaurantController extends AbstractActionController
{
    protected $restaurantTable;
    protected $addressTable;
    protected $session;
    /**
     * The default action - show the home page
     */
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    public function editAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
            
            $id = $this->session->restaurantID;
            
            $restaurant = $this->getRestaurantTable()->getRestaurant($id);
            $addresses = $this->getAddressTable()->getAddresses('restaurant', $id);
            $address = $this->getAddressTable()->getAddressByType('restaurant', $id);
        
            $form  = new RestaurantForm();
            $form->bind($restaurant);
            $form->get('submit')->setAttribute('value', 'Edit');
    
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setInputFilter($restaurant->getInputFilter());
                $form->setData($request->getPost());
        
                if ($form->isValid()) {
                    $this->getRestaurantTable()->saveRestaurant($restaurant);
        
                    return $this->redirect()->toRoute('restprofile', array(
                                                        'action' => 'change'
                                                    ));
                }
            }
    
            return array(
                'id' => $id,
                'form' => $form,
                'restaurant' => $restaurant,
                'address' => $address,
                'addresses' => $addresses,
            );
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
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
}
