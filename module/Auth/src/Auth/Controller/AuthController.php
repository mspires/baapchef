<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Auth\Form\LoginForm;
use Auth\Form\Filter\LoginFilter;
use Auth\Utility\UserPassword;
use Zend\Session\Container;

class AuthController extends AbstractActionController
{
    public function indexAction()
    {
        return array();
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        
        $key = $this->params()->fromRoute('key', sha1('customer'));
        
        $view = new ViewModel();
        $loginForm = new LoginForm('loginForm');
        $loginForm->setInputFilter(new LoginFilter());
        
        $loginForm->get('role')->setValue($key);
        
        if ($request->isPost()) {
            $data = $request->getPost();
            $loginForm->setData($data);
            
            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
                
                $authService = $this->getServiceLocator()->get('AuthService');
                
                $authService->getAdapter()
                    ->setIdentity($data['email'])
                    ->setCredential($encyptPass);
                
                $result = $authService->authenticate();
                
                if ($result->isValid()) {
                    
                    $userDetails = $this->_getUserDetails(array(
                        'email' => $data['email']
                    ), array(
                        'id'
                    ));
                    
                    $session = new Container('User');
                    $session->offsetSet('email', $data['email']);
                    $session->offsetSet('userId', $userDetails[0]['id']);
                    $session->offsetSet('roleId', $userDetails[0]['role_id']);
                    $session->offsetSet('roleName', $userDetails[0]['role_name']);
                    
                    $this->flashMessenger()->addMessage(array(
                        'success' => 'Login Success.'
                    ));
                    // Redirect to page after successful login
                } else {
                    $this->flashMessenger()->addMessage(array(
                        'error' => 'invalid credentials.'
                    ));
                    // Redirect to page after login failure
                }
                return $this->redirect()->toUrl('/login', array('key' => $key));
                // Logic for login authentication
            } else {
                $errors = $loginForm->getMessages();
                // prx($errors);
            }
        }
        
        $view->setVariable('loginForm', $loginForm);
        return $view;
    }

    public function logoutAction(){
        
        $key = $this->params()->fromRoute('key', 'admin');
        
        $authService = $this->getServiceLocator()->get('AuthService');
        
        $session = new Container('User');
        $session->getManager()->destroy();
        
        $authService->clearIdentity();

        return $this->redirect()->toUrl('/login', array('key' => $key));
    }
    
    public function signupAction()
    {
        $request = $this->getRequest();
    
        $key = $this->params()->fromRoute('key', sha1('customer'));
    
        $view = new ViewModel();
        $signupForm = new LoginForm('signupForm');
        $signupForm->setInputFilter(new LoginFilter());
    
        $signupForm->get('role')->setValue($key);
    
        if ($request->isPost()) {
            $data = $request->getPost();
            $signupForm->setData($data);
    
            if ($signupForm->isValid()) {
                $data = $signupForm->getData();
    
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
    
                return $this->redirect()->toUrl('/login', array('key' => $key));
            } else {
                $errors = $signupForm->getMessages();
            }
        }
    
        $view->setVariable('signupForm', $signupForm);
        return $view;
    }    
    
    private function _getUserDetails($where, $columns)
    {
        $userTable = $this->getServiceLocator()->get("UserTable");
        $users = $userTable->getUsers($where, $columns);
        return $users;
    }
}
