<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Auth\Form\LoginForm;
use Auth\Form\Filter\LoginFilter;
use Auth\Form\SignupForm;
use Auth\Form\Filter\SignupFilter;
use Auth\Utility\UserPassword;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class AuthController extends AbstractActionController
{
    protected $session;
    protected $logger;
    
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
                
                $userPassword = new UserPassword('plain');
                $encyptPass = $userPassword->create($data['password']);
                
                $authService = $this->getServiceLocator()->get('AuthService');
                
                $authService->getAdapter()
                    ->setTableName('admin')
                    ->setIdentity($data['email'])
                    ->setCredential($encyptPass);
                
                $result = $authService->authenticate();
                
                if ($result->isValid()) {
                    
                    $row = $authService->getAdapter()->getResultRowObject();
                    
                    $this->session->offsetSet('email', $row->email);
                    $this->session->offsetSet('adminID', $row->id);
                    
                    $this->flashMessenger()->addMessage(array(
                        'success' => 'Login Success.'
                    ));
                    return $this->redirect()->toRoute('adminhome');
                }
            } else {
                $errors = $loginForm->getMessages();
            }
        }
        
        $view->setVariable('loginForm', $loginForm);
        return $view;
    }

    public function logoutAction(){
        
        $authService = $this->getServiceLocator()->get('AuthService');
        
        $this->session->getManager()->destroy();
        
        $authService->clearIdentity();

        return $this->redirect()->toRoute('adminhome');
    }    

    public function pininAction(){

        $request = $this->getRequest();
        
        $valid = false;
        $message = 'invalid PIN.';
        
        if ($request->isPost()) {
            
            $data = $request->getPost();
            
            $authService = $this->getServiceLocator()->get('AuthService');
            
            $authService->getAdapter()
                ->setIdentity($data['user'])
                ->setCredential($data['pin']);
            
            $result = $authService->authenticate();
            
            if ($result->isValid()) {
            
                $userDetails = $this->_getUserDetails(array(
                    'email' => $data['user']
                ), array(
                    'id'
                ));
                
                $this->session->offsetSet('roleId', $userDetails[0]['role_id']);
                $this->session->offsetSet('roleName', $userDetails[0]['role_name']);
                
                $valid = true;
                $message = $userDetails[0]['role_name'];
            }           
        }
        
        $rtn = array(  'valid'=>$valid,
            'message'=>$message
        );
        
        $result = new JsonModel($rtn);
        
        return $result;
    }
    
    public function pinoutAction(){
    
        $this->session->offsetSet('roleId', '');
        $this->session->offsetSet('roleName', '');        
        $this->session->roleId = null;
        $this->session->roleName = null;
        
        return $this->redirect()->toRoute('adminhome');
    }
    
    private function _getUserDetails($where, $columns)
    {
        $userTable = $this->getServiceLocator()->get("UserTable");
        $users = $userTable->getUsers($where, $columns);
        return $users;
    }
}
