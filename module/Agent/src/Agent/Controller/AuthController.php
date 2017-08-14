<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Agent\Controller;

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
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Auth\Model\Role;

use Agent\Model\Agent;

use Agent\Model\SendEmail;

class AuthController extends AbstractActionController
{
    protected $agentTable;
    
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
                    ->setTableName('agent')
                    ->setIdentity($data['email'])
                    ->setCredential($encyptPass);
                
                $result = $authService->authenticate();
                
                if ($result->isValid()) {
                    
                    $row = $authService->getAdapter()->getResultRowObject();
                    
                    if($row->status == 'Y')
                    {
                        $this->session->offsetSet('email', $row->email);
                        $this->session->offsetSet('agentID', $row->id);
                        
                        $this->flashMessenger()->addMessage(array(
                            'success' => 'Login Success.'
                        ));
                        return $this->redirect()->toRoute('agenthome');
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

    public function logoutAction(){
        
        $authService = $this->getServiceLocator()->get('AuthService');
        
        $this->session->getManager()->destroy();
        
        $authService->clearIdentity();

        return $this->redirect()->toRoute('agenthome');
    }
    
    public function signupAction()
    {
        $request = $this->getRequest();
    
        $view = new ViewModel();
        $signupForm = new SignupForm('signupForm');
        $signupForm->setInputFilter(new LoginFilter());
    
          if ($request->isPost()) {
            $data = $request->getPost();
            $signupForm->setData($data);
    
            if ($signupForm->isValid()) {
                $data = $signupForm->getData();
    
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);

                $data['password'] = $encyptPass;
                $data['level'] = 1;
                $data['status'] = 'N';
                
                $agent = new Agent();
                $agent->exchangeArray($data);
                $this->getAgentTable()->saveAgent($agent);

                $email = new SendEmail($this);
                $email->signup($agent);
    
                return $this->redirect()->toRoute('agenthome');
            } else {
                $errors = $signupForm->getMessages();
            }
        }
    
        $view->setVariable('signupForm', $signupForm);
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
            ->setTableName('agent')
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
                     
                    $agent = new Agent();
                    $agent->exchangeArray($data2);
                    $this->getAgentTable()->saveAgent($agent);
            
                    $view = new ViewModel();
                    $view->setTemplate('Agent/profile/change');
            
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
        return $view;
    }
    
    public function registerAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
    
            $data = $request->getPost()->toArray();
            
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($adapter);
            
            if ($this->session->offsetExists('authcode')) {

                if($this->session->authcode == $data['code']) 
                {
                    $view = new ViewModel();
                    $view->setTemplate('Agent/home/passwd');
                    
                    return $view;
                }  
                else 
                {
                    $view = new ViewModel();
                    return $view;                    
                }
            }
            
            $userPassword = new UserPassword();
            
            $name = $data['name'];
            $userid = $data['email'];
            $email = $data['email'];
            $password = $userPassword->generate(8);
            $encyptPass = $userPassword->create($password);
            $phone = $data['phone'];
          
            $select = $sql->select();
            $select->from('agent');
            $select->where(array('userid' => $email));
            
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $row = $result->current();
            if (!$row) {
                $agent = array( 'name'=>$name,
                                'userid'=>$email,
                                'email'=>$email,
                                'password'=>$encyptPass,
                                'phone'=>$phone,
                                'level'=>1,
                                'status'=>'N',
                              );
                $insert = $sql->insert('agent');
                $insert->values($agent);
                
                $statement = $sql->prepareStatementForSqlObject($insert);
                $statement->execute();
                
                $select = $sql->select();
                $select->from('agent');
                $select->where(array('email' => $email));
                
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                
                $row = $result->current();
                if ($row) {

                    $id = $row['id'];
                    $password = $userPassword->generatePIN(4);
                    
                    $user = array('usertype'=>'Agent',
                            'rid'=>$id,
                            'name'=>'admin',
                            'userid'=>'Admin',
                            'email'=>$email,
                            'password'=>$password,
                            'phone'=>$phone,
                            'status'=>'Y' 
                        );
                        
                    $insert = $sql->insert('users');
                    $insert->values($user);
                    
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $statement->execute();
                    
                    $userid = $adapter->getDriver()->getLastGeneratedValue();
                    
                    $insert = $sql->insert('user_role');
                    $insert->values(array('user_id'=>$userid, 'role_id' => ROLE::ROLE_ADMIN ));
                    
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $statement->execute();
                    
                    $destination = sprintf('%s/%09d/','./public/data/agent',$id);
                    
                    if (!file_exists($destination)) {
                    
                        mkdir($destination, 0777, true);
                    }
                    
                    $logo = sprintf('./data/data/agent/%09d/%s.png', $id,'agent');
                    copy('./data/data/agent/agent.png', $logo);
                    
                    $address = '';
                    $city = $data['city'];
                    $state = $data['state'];
                    $zip = $data['zipcode'];
                    $country = $data['country'];
                    
                    $insert = $sql->insert('address');
                    $insert->values(array('type'=>'agent',
                                            'tid'=>$id,
                                            'address1'=>$address,
                                            'city'=>$city,
                                            'state'=>$state,
                                            'zip'=>$zip,
                                            'country'=>$country ));
                                        
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $statement->execute();

                    $this->session->authcode = $userPassword->generatePIN(4);
                    
                    $email = new SendEmail($this);
                    $email->register($agent, $user, $this->session->authcode);                    
                    
                }
            }
        }
    
        $view = new ViewModel();
        return $view;
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
                    'userid' => $data['user']
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
    
        return $this->redirect()->toRoute('agenthome');
    }
    
    public function getAgentTable()
    {
        if (!$this->agentTable) {
            $sm = $this->getServiceLocator();
            $this->agentTable = $sm->get('Agent\Model\AgentTable');
        }
        return $this->agentTable;
    }
    
    private function _getUserDetails($where, $columns)
    {
        $userTable = $this->getServiceLocator()->get("UserTable");
        $users = $userTable->getUsers($where, $columns);
        return $users;
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
