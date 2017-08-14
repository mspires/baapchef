<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
use Zend\Db\ResultSet\ResultSet;

use Auth\Form\LoginForm;
use Auth\Form\Filter\LoginFilter;
use Auth\Form\SignupForm;
use Auth\Form\Filter\SignupFilter;
use Auth\Utility\UserPassword;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Zend\Session\Container;
use Zend\Log\Logger;

use Customer\Model\Customer;

use Customer\Model\SendEmail;

class AuthController extends AbstractActionController
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
        return array();
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        
        $view = new ViewModel();
        $loginForm = new LoginForm('loginForm');
        $loginForm->setInputFilter(new LoginFilter());
        
        if ($request->isPost()) {
            $data = $request->getPost();
            $loginForm->setData($data);
            
            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
                
                $authService = $this->getServiceLocator()->get('AuthService');
                
                $authService->getAdapter()
                    ->setTableName('customer')
                    ->setIdentity($data['email'])
                    ->setCredential($encyptPass);
                
                $result = $authService->authenticate();
                
                if ($result->isValid()) {
                    
                    $row = $authService->getAdapter()->getResultRowObject();
                    
                    if($row->status == 'Y')
                    {
                        $this->session->offsetSet('email', $row->email);
                        $this->session->offsetSet('customerID', $row->id);
                        
                        $this->flashMessenger()->addMessage(array(
                            'success' => 'Login Success.'
                        ));
                        
                        return $this->redirect()->toRoute('customerhome');
                    }
                    else {
                        $this->flashMessenger()->addMessage(array(
                            'error' => 'account is disabled.'
                        ));                        
                        
                    }
                } else {
                    $this->flashMessenger()->addMessage(array(
                        'error' => 'invalid credentials.'
                    ));
                }
                
            } else {
                $errors = $loginForm->getMessages();
                
            }
        }
        
        $view->setVariable('loginForm', $loginForm);
        return $view;
    }

    public function logindoAction()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeaders($headers);
        
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        Json::$useBuiltinEncoderDecoder = true;
    
        $request = $this->getRequest();
    
        $id = 0;
        $result = 'FAILED';
        $message = '';
        
        if ($request->isPost()) {
    
            $data = $request->getPost();
            if(!is_array($data))
            {
                $data = json_decode($this->getRequest()->getContent(),true);
            }
            
            $userPassword = new UserPassword();
            $encyptPass = $userPassword->create($data['password']);
    
            $authService = $this->getServiceLocator()->get('AuthService');
            
            $authService->getAdapter()
                ->setTableName('customer')
                ->setIdentity($data['email'])
                ->setCredential($encyptPass);
                
            $result = $authService->authenticate();
            
            if ($result->isValid()) {
                
                $row = $authService->getAdapter()->getResultRowObject();
                
                if($row->status == 'Y')
                {
                    $this->session->offsetSet('email', $row->email);
                    $this->session->offsetSet('customerID', $row->id);
                    
                    $id = $row->id;
                    $result = 'OK';
                }
            }
            else 
            {
                
                $message = 'invalid credentials.';
            }
            
        }
        return $view->setVariables(array(
            'result' => $result,
            'message' => $message, 
            'customer' => $this->getCustomerTable()->getCustomer($id),
        ));
    }
    
    public function logoutAction(){
        
        $authService = $this->getServiceLocator()->get('AuthService');
        
        $this->session->getManager()->destroy();
        $authService->clearIdentity();

        return $this->redirect()->toRoute('customerhome');
    }
    
    public function signupAction()
    {
        $request = $this->getRequest();
    
        $view = new ViewModel();
        $signupForm = new SignupForm('signupForm');
        $signupForm->setInputFilter(new SignupFilter());
     
        if ($request->isPost()) {
            $data = $request->getPost();
            $signupForm->setData($data);
    
            if ($signupForm->isValid()) {
                $data = $signupForm->getData();
                
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);

                $data['password'] = $encyptPass;
                $data['level'] = 1;
                $data['status'] = 'Y';
                
                $customer = new Customer();
                $customer->exchangeArray($data);
                $this->getCustomerTable()->saveCustomer($customer);

                $email = new SendEmail($this);
                $email->signup($customer);
                
                $this->session->authcode = $userPassword->generatePIN(4);
                
                $view = new ViewModel();
                $view->setTemplate('Customer/auth/register');
        
                return $view;
                
                //return $this->redirect()->toRoute('customerhome');
                
            } else {
                $errors = $signupForm->getMessages();
            }
        }
    
        $view->setVariable('signupForm', $signupForm);
        return $view;
    }    
    
    public function signupdoAction()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeaders($headers);
        
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        Json::$useBuiltinEncoderDecoder = true;
        
        $request = $this->getRequest();        
       
        $result = 'FAILED';
        $message = '';
        $customer = null;
        
        if ($request->isPost()) {

            $data = $request->getPost();
            if(!is_array($data))
            {
                $data = json_decode($this->getRequest()->getContent(),true);
            }
            
            if($this->getCustomerTable()->isCustomerExist($data['email']) == false)
            {
                            
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
    
                $data['password'] = $encyptPass;
                $data['level'] = 1;
                $data['status'] = 'N';
                $data['authcode'] = $userPassword->generatePIN(4);
                
                $customer = new Customer();
                $customer->exchangeArray($data);
                $this->getCustomerTable()->saveCustomer($customer);
                $result = 'OK';
                
                $email = new SendEmail($this);
                $email->signup($customer);
            }
            else 
            {
                $message = 'account is duplicated.';
            }
        }
    
        return $view->setVariables(array(
            'result' => $result,
            'message' => $message,
            'customer' => $customer
        ));
    }

    public function registerAction()
    {
        $request = $this->getRequest();
        
        $view = new ViewModel();
         
        if ($request->isPost()) {

            $data = $request->getPost();
        
            if ($this->session->offsetExists('authcode')) {
    
                if($this->session->authcode == $data['code'])
                {
                    $view->setTemplate('Customer/home/passwd');
                    return $view;
                }
            }
        }
        else
        {
            $auth = $this->params()->fromQuery('auth',null);
        
            if($auth != null)
            {
                $this->session->authcode = $code;
            }
        }
        
        return $view;        
    } 
    
    public function changepwdAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
    
            $data = $request->getPost()->toArray();
    
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($adapter);
            
            $userPassword = new UserPassword();
            $encyptPass = $userPassword->create($data['oldpwd']);
            
            $authService = $this->getServiceLocator()->get('AuthService');
            
            $authService->getAdapter()
            ->setTableName('customer')
            ->setIdentity($data['email'])
            ->setCredential($encyptPass);
            
            $result = $authService->authenticate();
            
            if ($result->isValid()) {
            
                $row = $authService->getAdapter()->getResultRowObject();
            
                if($row->status == 'Y')
                {
                    $encyptPass = $userPassword->create($data['confirmpwd']);
                    
                    $data2 = array(
                        'id' => $row->id,
                        'name' => $row->name,
                        'userid' => $row->email,
                        'email' => $row->email,
                        'phone' => $row->phone,
                        'password' => $encyptPass,
                        'level' => $row->level,
                        'status' => $row->status,
                        'note'  => $row->note,                        
                    );
                   
                    $customer = new Customer();
                    $customer->exchangeArray($data2);
                    $this->getCustomerTable()->saveCustomer($customer);
                    
                    $view = new ViewModel();
                    $view->setTemplate('customer/profile/change');
                    
                    return $view;
                }
                else {
                    $this->flashMessenger()->addMessage(array(
                        'error' => 'account is disabled.'
                    ));
                }
            } else {
                $this->flashMessenger()->addMessage(array(
                    'error' => 'invalid credentials.'
                ));
            }
        }

        $view = new ViewModel();
        $view->setTemplate('passwd');

        return $view;
    }
    
    public function changepwddoAction()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeaders($headers);
        
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        Json::$useBuiltinEncoderDecoder = true;
                
        $request = $this->getRequest();
        
        $result = 'FAILED';
        $message = '';
        $id = 0;
        
        if ($request->isPost()) {
    
            $data = $request->getPost();
            if(!is_array($data))
            {
                $data = json_decode($this->getRequest()->getContent(),true);
            }
                
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($adapter);
    
            $userPassword = new UserPassword();
            $encyptPass = $userPassword->create($data['oldpwd']);
    
            $authService = $this->getServiceLocator()->get('AuthService');
    
            $authService->getAdapter()
            ->setTableName('customer')
            ->setIdentity($data['email'])
            ->setCredential($encyptPass);
    
            $result = $authService->authenticate();
    
            if ($result->isValid()) {
    
                $row = $authService->getAdapter()->getResultRowObject();
    
                if($row->status == 'Y')
                {
                    $encyptPass = $userPassword->create($data['confirmpwd']);
    
                    $data2 = array(
                        'id' => $row->id,
                        'name' => $row->name,
                        'userid' => $row->email,
                        'email' => $row->email,
                        'phone' => $row->phone,
                        'password' => $encyptPass,
                        'level' => $row->level,
                        'status' => $row->status,
                        'note'  => $row->note,
                    );
                     
                    $customer = new Customer();
                    $customer->exchangeArray($data2);
                    $this->getCustomerTable()->saveCustomer($customer);
                
                    $id = $row->id;
                    $result = 'OK';
                }
                else {
                    $message = 'account is disabled.';
                    
                }
            } else {
                $message = 'invalid credentials.';
            }
        }

        return $view->setVariables(array(
            'result' => $result,
            'message' => $message, 
            'customer' => $this->getCustomerTable()->getCustomer($id),
        ));
    }
    
    public function forgotpwdAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
    
            $data = $request->getPost();
    
    
        }
    
        $view = new ViewModel();
        return $view;
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
