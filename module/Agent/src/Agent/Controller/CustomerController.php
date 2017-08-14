<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Customer for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Agent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Customer\Model\Customer;
use Customer\Form\CustomerForm;
use Restaurant\Model\Restaurant;
use Auth\Utility\UserPassword;

use Zend\Session\Container;
use Zend\Log\Logger;

class CustomerController extends AbstractActionController
{
    protected $customerTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('agentID')) {
        
            $rid = (int) $this->params()->fromRoute('id', 0);
            $aid = $this->session->agentID;
            $current_restaurant = null;
            $restaurants = $this->getRestaurantTable()->getRestaurants($aid);
            if($rid != 0)
            {
                $this->session->restaurantID = $rid;
                $current_restaurant = $this->getRestaurantTable()->getRestaurant($rid);
            }
            
            return new ViewModel(array(
                'dishes' => $this->getDishTable()->getDishes($rid),
                'restaurants' => $restaurants,
                'current_restaurant' => $current_restaurant,
            ));
        }
        else
        {
        
            return $this->redirect()->toRoute('agenthome');
        }        
    }
    
    public function addAction()
    {
        $form = new CustomerForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $customer = new Customer();
            $form->setInputFilter($customer->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $customer->exchangeArray($form->getData());
                $cid = $this->getCustomerTable()->saveCustomer($customer);
    
                $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                $sql = new Sql($adapter);
                
                $insert = $sql->insert('customermap');
                $insert->values(array('cid'=>$cid,
                    'rid'=>$rid,
                    'status'=>'Y',
                ));
                
                $statement = $sql->prepareStatementForSqlObject($insert);
                $statement->execute();
                //send confirmation email w/ password;
                return $this->redirect()->toRoute('agcustomer');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('agcustomer', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $customer = $this->getCustomerTable()->getCustomer($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('agcustomer', array(
                'action' => 'index'
            ));
        }
    
        $form  = new CustomerForm();
        $form->bind($customer);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($customer->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getCustomerTable()->saveCustomer($customer);
    
                return $this->redirect()->toRoute('agcustomer');
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
            return $this->redirect()->toRoute('customer');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getCustomerTable()->deleteCustomer($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('customer');
        }
    
        return array(
            'id'    => $id,
            'album' => $this->getCustomerTable()->getCustomer($id)
        );
    }
        
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurantTable;
    }
    
    public function getCustomerTable()
    {
        if (!$this->customerTable) {
            $sm = $this->getServiceLocator();
            $this->customerTable = $sm->get('Customer\Model\CustomerTable');
        }
        return $this->customerTable;
    }
}
