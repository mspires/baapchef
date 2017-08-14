<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Restaurant\Model\Restaurant;
use Restaurant\Form\RestaurantForm;
use Zend\Session\Container;
use Zend\Log\Logger;

use Zend\File\Transfer\Adapter;

use Auth\Utility\UserPassword;
use Admin\Model\SendEmail;

class RestaurantController extends AbstractActionController
{
    protected $restaurantTable;
    protected $addressTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('adminID')) {
            
            return new ViewModel(array(
                'restaurants' => $this->getRestaurantTable()->fetchAll(),
            ));
        }
        else
        {
            return $this->redirect()->toRoute('adminhome');
        }   
    }
    
    public function addAction()
    {
        $form = new RestaurantForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $restaurant = new Restaurant();
            $form->setInputFilter($restaurant->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $restaurant->exchangeArray($form->getData());
                $this->getRestaurantTable()->saveRestaurant($restaurant);
    
                return $this->redirect()->toRoute('adminrestaurant');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminrestaurant', array(
                'action' => 'add'
            ));
        }
        
        $this->session->restaurantID = $id;
    
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
                return $this->redirect()->toRoute('adminrestaurant');
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
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminrestaurant');
        }

        $this->getRestaurantTable()->deleteRestaurant($id);
        return $this->redirect()->toRoute('adminrestaurant');
    }

    public function uploaddragfileAction()
    {
        $valid = false;
        $name = "";
        $url = "";
        $message = "";
    
        $rid = $this->session->restaurantID;
    
        if ($this->getRequest()->isPost())
        {
            $postdata = $this->getRequest()->getPost()->toArray();
    
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $destination = sprintf('./data/data/%09d',$rid);
            $adapter->setDestination($destination);
    
            $files  = $adapter->getFileInfo();
            foreach($files as $file => $fileInfo) {
    
                if($postdata['dishfilename'] == null)
                {
                    $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                    $newFilename = 'logo' . '.' . $ext;
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
                    $imagePath = sprintf('/data/restaurant/%09d/%s', $rid, $newFilename);
    
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
    
            if ($this->session->offsetExists('restaurantID')) {
    
                $id = $this->session->restaurantID;
    
                $userPassword = new UserPassword();
                $password = $userPassword->generate(8);
                $encyptPass = $userPassword->create($password);
    
                $restaurant = $this->getRestaurantTable()->getRestaurant($id);
                $restaurant->password = $encyptPass;
    
                $this->getRestaurantTable()->saveRestaurant($restaurant);
    
                $email = new SendEmail($this);
                $email->resetpwd($restaurant, $password);
    
                $valid = true;
            }
            else 
            {
                $message = "no restaurant id";
                
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
        
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurant = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurant;
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