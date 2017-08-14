<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Restaurant\Model\Restaurant;
use Dish\Model\TakeoutDish;
use Dish\Form\TakeoutDishForm;
use Zend\Session\Container;
use Zend\Log\Logger;

use Zend\File\Transfer\Adapter;
use Zend\View\Model\JsonModel;

class TakeoutDishController extends AbstractActionController
{
    protected $takeoutdishTable;
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
                'dishes' => $this->getTakeoutDishTable()->getDishes($rid),
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
        $form = new TakeoutDishForm();
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setValue($this->session->restaurantID);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dish = new TakeoutDish();
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $dish->exchangeArray($form->getData());
                $this->getTakeoutDishTable()->saveDish($dish);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('admintakeoutdish');
            }
        }
        return array('form' => $form);        
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admintakeoutdish', array(
                'action' => 'add'
            ));
        }
        
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $dish = $this->getTakeoutDishTable()->getDish($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('admintakeoutdish', array(
                'action' => 'index'
            ));
        }
        
        $form = new TakeoutDishForm();
        $form->get('rid')->setValue($this->session->restaurantID);
        
        $form->bind($dish);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $this->getTakeoutDishTable()->saveDish($dish);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('admintakeoutdish');
            }
        }
        
        return array(
            'id' => $id,
            'form' => $form,
            'dish' => $this->getTakeoutDishTable()->getDish($dish->id),
        );        
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admintakeoutdish');
        }
        
        $this->getTakeoutDishTable()->deleteDish($id);
        return $this->redirect()->toRoute('admintakeoutdish');
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
                    $newFilename = date('YmdHi') . '-' . uniqid() . '.' . $ext;
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
                    $imagePath = sprintf('/data/%09d/%s', $rid, $newFilename);
    
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
    
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurantTable;
    }
     
    public function getTakeoutDishTable()
    {
        if (!$this->takeoutdishTable) {
            $sm = $this->getServiceLocator();
            $this->takeoutdishTable = $sm->get('Dish\Model\TakeoutDishTable');
        }
        return $this->takeoutdishTable;
    }
}