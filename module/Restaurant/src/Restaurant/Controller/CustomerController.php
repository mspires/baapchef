<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Customer for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Customer\Model\Customer;
use Customer\Form\CustomerForm;
use Auth\Utility\UserPassword;

use Zend\Db\Sql\Sql;

use Zend\Session\Container;
use Zend\Log\Logger;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;

class CustomerController extends AbstractActionController
{
    protected $customerTable;
    
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
            
            $id = $this->session->restaurantID;
            return new ViewModel(array(
                'customers' => $this->getCustomerTable()->getCustomers($id),
            ));   
        }
        else 
        {
            return $this->redirect()->toRoute('restauranthome');
        }
    }
    
    public function listAction()
    {
        
        $request = $this->getRequest();
        
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        
        Json::$useBuiltinEncoderDecoder = true;
        
        $id = $this->session->restaurantID;
        
        if ($request->isPost()) {
        
            $data = $request->getPost();
            
            $view->setVariables(array(
                'customers' => $this->getCustomerTable()->getCustomers($id),
            ));
        }
        else
        {
            $view->setVariables(array(
                'customers' => $this->getCustomerTable()->getCustomers($id),
            ));
        }
        
        return $view;    
    }
    
    public function addAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
        
            $rid = $this->session->restaurantID;
                    
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
                    return $this->redirect()->toRoute('restcustomer');
                }
            }
            return array('form' => $form);
        }
    }
    
    public function editAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
        
            $rid = $this->session->restaurantID;
                    
            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                return $this->redirect()->toRoute('restcustomer', array(
                    'action' => 'add'
                ));
            }
        
            // Get the Album with the specified id.  An exception is thrown
            // if it cannot be found, in which case go to the index page.
            try {
                $customer = $this->getCustomerTable()->getCustomer($id);
            }
            catch (\Exception $ex) {
                return $this->redirect()->toRoute('restcustomer', array(
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
        
                    return $this->redirect()->toRoute('restcustomer');
                }
            }
        
            return array(
                'id' => $id,
                'form' => $form,
                'customer' => $customer,
            );
        }
    }
    
    public function deleteAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
        
            $rid = $this->session->restaurantID;
            
            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                return $this->redirect()->toRoute('restcustomer');
            }
    
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($adapter);
            
            $delete = $sql->delete('customermap');
            $delete->where(array('cid'=>$id,
                'rid'=>$rid,
            ));
                    
            $statement = $sql->prepareStatementForSqlObject($delete);
            $statement->execute();
                    
            return $this->redirect()->toRoute('restcustomer');
        }
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
