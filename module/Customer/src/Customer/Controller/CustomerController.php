<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Customer for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Customer\Model\Customer;
use Customer\Form\CustomerForm;

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
     
    public function editAction()
    {
        if ($this->session->offsetExists('customerID')) {
 
            $id = $this->session->customerID;
    
            $customer = $this->getCustomerTable()->getCustomer($id);
        
            $form  = new CustomerForm();
            $form->bind($customer);
            $form->get('submit')->setAttribute('value', 'Edit');
        
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setInputFilter($customer->getInputFilter());
                $form->setData($request->getPost());
        
                if ($form->isValid()) {
                    $this->getCustomerTable()->saveCustomer($customer);
        
                    $view = new ViewModel();
                    $view->setTemplate('customer/profile/change');
                    
                    return $view;
                }
            }
        
            return array(
                'id' => $id,
                'form' => $form,
                'customer' => $customer,
            );
        }
        else 
        {
            return $this->redirect()->toRoute('customerhome');
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
