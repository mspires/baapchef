<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Auth\Form\LoginForm;
use Auth\Form\Filter\LoginFilter;
use Auth\Form\SignupForm;
use Auth\Form\Filter\SignupFilter;
use Auth\Utility\UserPassword;
use Zend\Session\Container;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
use Zend\Db\ResultSet\ResultSet;

//use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Auth\Model\Role;

use GoogleMapsTools\Api\Geocode;
use GoogleMapsTools\Api;

use Restaurant\Model\Restaurant;
use Restaurant\Model\Address;

class AuthController extends AbstractActionController
{
    protected $restaurantTable;
    
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
                    ->setTableName('restaurant')
                    ->setIdentity($data['email'])
                    ->setCredential($encyptPass);
                
                $result = $authService->authenticate();
                
                if ($result->isValid()) {
                    
                    $row = $authService->getAdapter()->getResultRowObject();
                    
                    if($row->status == 'Y')
                    {
                        $this->session->offsetSet('email', $row->email);
                        $this->session->offsetSet('restaurantID', $row->id);
                        
                        $this->flashMessenger()->addMessage(array(
                            'success' => 'Login Success.'
                        ));
                        return $this->redirect()->toRoute('restauranthome');
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
            ->setTableName('restaurant')
            ->setIdentity($data['email'])
            ->setCredential($encyptPass);
    
            $result = $authService->authenticate();
    
            if ($result->isValid()) {
    
                $row = $authService->getAdapter()->getResultRowObject();
    
                if($row->status == 'Y')
                {
                    $this->session->offsetSet('email', $row->email);
                    $this->session->offsetSet('restaurantID', $row->id);
    
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
            'restaurant' => $this->getRestaurantTable()->getRestaurant($id),
        ));
    }
    
    public function logoutAction(){
         
        $authService = $this->getServiceLocator()->get('AuthService');
        
        $this->session->getManager()->destroy();
        
        $authService->clearIdentity();

        return $this->redirect()->toRoute('restauranthome');
    }
    
    public function signupAction()
    {
        return $this->redirect()->toRoute('restaurant', array('action' => 'add'));
        /*
        $request = $this->getRequest();
    
    
        $view = new ViewModel();
        $signupForm = new SignupForm('signupForm');
        $signupForm->setInputFilter(new SignupFilter());
    
    
        if ($request->isPost()) {
            $data = $request->getPost();
            $signupForm->setData($data);
    
            if ($signupForm->isValid()) {
                
                $data = $signupForm->getData();
    
                $restaurant = new Restaurant();
                $restaurant->exchangeArray($data);
                $this->getRestaurantTable()->saveRestaurant($restaurant);
                
                return $this->redirect()->toRoute('restauranthome');
            } else {
                $errors = $signupForm->getMessages();
            }
        }
    
        $view->setVariable('signupForm', $signupForm);
        return $view;
        */
    }    
    
    public function registerAction()
    {
        /*
        try {
            $this->getLogger()->Info("Send Email now");
            
            $transport = $this->getServiceLocator()->get('mail.transport');
            
            $message = new Message();
            
            $message->addTo('eaglemay@gmail.com')
                        ->setSubject('Greetings and Salutations!')
                        ->setBody("Sorry, I'm going to be late today!");
                  
            $transport->send($message);
            
        }
        catch (\Exception $ex) {

            $this->getLogger()->Info($ex->getMessage());
        }
        */
        
        $request = $this->getRequest();
        if ($request->isPost()) {
    
            $data = $request->getPost()->toArray();
            
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($adapter);
            
            $userPassword = new UserPassword();
            
            $name = $data['name'];
            $email = $data['email'];
            $userid = $data['email'];
            $password = $userPassword->generate(8);
            $phone = $data['phone'];
            $tax = $data['tax'] / 100;
            
            $select = $sql->select();
            $select->from('restaurant');
            $select->where(array('email' => $email));
            
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $row = $result->current();
            if (!$row) {
                
                $insert = $sql->insert('restaurant');
                $insert->values(array('name'=>$name,
                                        'userid'=>$userid,
                                        'email'=>$email,
                                        'password'=>$password,
                                        'phone'=>$phone,
                                        'level'=>1,
                                        'status'=>'N',
                                        'tax'=>$tax,
                 ));
                
                $statement = $sql->prepareStatementForSqlObject($insert);
                $statement->execute();
                
                $select = $sql->select();
                $select->from('restaurant');
                $select->where(array('email' => $email));
                
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                
                $row = $result->current();
                if ($row) {

                    $id = $row['id'];
                    $password = $userPassword->generatePIN(4);
                    
                    $insert = $sql->insert('users');
                    $insert->values(array('usertype'=>'Restaurant',
                        'rid'=>$id,
                        'name'=>'admin',
                        'userid'=>'Admin',
                        'password'=>$password,
                        'email'=>$email,
                        'phone'=>$phone,
                        'status'=>'Y' ));
                    
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $statement->execute();
                    
                    $userid = $adapter->getDriver()->getLastGeneratedValue();
                    
                    $insert = $sql->insert('user_role');
                    $insert->values(array('user_id'=>$userid, 'role_id' => ROLE::ROLE_ADMIN ));
                    
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $statement->execute();
                    
                    $destination = sprintf('%s/%09d','./data/data',$id);
                    $destination2 = sprintf('%s/%09d','./data/data/restaurant',$id);
                    
                    if (!file_exists($destination)) {
                    
                        mkdir($destination, 0777, true);
                    }
                    if (!file_exists($destination2)) {
                    
                        mkdir($destination2, 0777, true);
                    }
                    
                    $logo = sprintf("./data/data/restaurant/%09d/%s.png", $id,'logo');
                    copy("./data/data/restaurant/logo.png", $logo);
                    
                    $agentid = 0;
                    if($agentid != 0)
                    {
                        $insert = $sql->insert('agentmap');
                        $insert->values(array('rid'=>$id,'agentid' => $agentid ));
                    
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $statement->execute();
                    }
                    
                    $address = new Address();
                    
                    $address->type = 'restaurant';
                    $address->tid = $id;
                    $address->address1 = $data['address1'];
                    $address->address2 = $data['address2'];
                    $address->city = $data['city'];
                    $address->state = $data['state'];
                    $address->zip = $data['zipcode'];
                    $address->country = $data['country'];
                    
                    $geocode = new Geocode($address->toString());
                    
                    $geocode->execute();
                    $point = $geocode->getFirstPoint();
                    
                    $address->lat = $point->getLatitude();
                    $address->lng =  $point->getLongitude();
                    
                    $insert = $sql->insert('address');
                    $insert->values(array('type'=>$address->type,
                                            'tid'=>$address->tid,
                                            'address1'=>$address->address1,
                                            'address2'=>$address->address2,
                                            'city'=>$address->city,
                                            'state'=>$address->state,
                                            'zip'=>$address->zip,
                                            'country'=>$address->country,
                                            'lat'=>$address->lat,
                                            'lng'=>$address->lng,
                     ));
                                        
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $statement->execute();                    
                    
                }
            }
        }
    
        $view = new ViewModel();
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
            ->setTableName('restaurant')
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
                     
                    $restaurant = new Restaurant();
                    $restaurant->exchangeArray($data2);
                    $this->getRestaurantTable()->saveRestaurant($restaurant);
    
                    $view = new ViewModel();
                    $view->setTemplate('Restaurant/profile/change');
    
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
        $view->setTemplate('Restaurant/home/passwd');
    
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
                    'email' => $data['user']
                ), array(
                    'id'
                ));
    
                $this->session->offsetSet('userID', $userDetails[0]['id']);
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
    
        return $this->redirect()->toRoute('restauranthome');
    }
    
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurantTable;
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
