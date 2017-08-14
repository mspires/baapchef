<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Customer for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Customer\Model\Customer;
use Customer\Form\CustomerForm;
use Auth\Utility\UserPassword;

use Zend\Db\Sql\Sql;

use Zend\Session\Container;
use Zend\Log\Logger;

use Admin\Model\SendEmail;

class CustomerController extends AbstractActionController
{
    protected $customerTable;
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
                'customers' => $this->getCustomerTable()->getCustomers($rid),
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

                $rid = $this->session->restaurantID;
                
                if($rid != 0)
                {
                    $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                    $sql = new Sql($adapter);
                    
                    $insert = $sql->insert('customermap');
                    $insert->values(array('cid'=>$cid,
                        'rid'=>$rid,
                        'status'=>'Y',
                    ));
                    
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $statement->execute();
                }               

                $email = new SendEmail($this);
                $email->signup($customer);
                
                return $this->redirect()->toRoute('admincustomer');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admincustomer', array(
                'action' => 'add'
            ));
        }
    
        $this->session->customerID = $id;
        
        try {
            $customer = $this->getCustomerTable()->getCustomer($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('admincustomer', array(
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
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('admincustomer');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
            'customer' => $customer,
        );
    }
    
    public function uploaddragfileAction()
    {
        $valid = false;
        $name = "";
        $url = "";
        $message = "";
    
        $id = $this->session->customerID;
    
        if ($this->getRequest()->isPost())
        {
            $postdata = $this->getRequest()->getPost()->toArray();
    
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $destination = sprintf('./data/data/customer/%09d',$id);
            $adapter->setDestination($destination);
    
            $files  = $adapter->getFileInfo();
            foreach($files as $file => $fileInfo) {
    
                if($postdata['dishfilename'] == null)
                {
                    $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                    $newFilename = 'customer' . '.' . $ext;
                }
                else
                {
                    $newFilename = $postdata['dishfilename'];
                }                
    
                $adapter->addFilter('Rename', array(
                    'target' => $newFilename,
                    'overwrite'  => true
                ));
    
                if (!$adapter->receive($fileInfo['name'])) {
    
                    $messages = $adapter->getMessages();
                }
                else {
    
                    //list($width, $height) = getimagesize("C:\xampp\htdocs\baapchef\public\data\" . $newFilename);
                    $imagePath = sprintf('/data/agent/%09d/%s', $id, $newFilename);
    
                    $valid = true;
                    $name = $newFilename;
                    $url = sprintf("<img src='%s?r=%d' style='width:200px' />", $imagePath,rand(1,100));
                }
            }
        }
    
        $data = array(  'valid'=>$valid,
            'name'=>htmlentities($name),
            'fullname'=>htmlentities($url),
            'url'=>$url,
            'message'=>$message
        );
    
        $result = new JsonModel($data);
    
        return $result;
    }
    
    public function ResetpwdAction()
    {
        $valid = false;
        $message = "";
    
        if ($this->session->offsetExists('adminID')) {
    
            if ($this->session->offsetExists('customerID')) {
    
                $id = $this->session->customerID;
    
                $userPassword = new UserPassword();
                $password = $userPassword->generate(8);
                $encyptPass = $userPassword->create($password);
    
                $customer = $this->getCustomerTable()->getCustomer($id);
                $customer->password = $encyptPass;
    
                $this->getCustomerTable()->saveCustomer($customer);
                
                $email = new SendEmail($this);
                $email->resetpwd($customer, $password);
    
                $valid = true;
            }
            else
            {
                $message = "no customer id";
    
            }
        }
        else
        {
            $message = "no admin id";
        }
    
        $data = array(  'valid'=>$valid,
            'message'=>$message
        );
    
        $result = new JsonModel($data);
    
        return $result;
    }
        
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admincustomer');
        }
    
        $this->getCustomerTable()->deleteCustomer($id);
        return $this->redirect()->toRoute('admincustomer');
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
