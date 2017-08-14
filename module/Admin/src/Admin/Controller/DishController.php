<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\Dish;
use Restaurant\Model\Restaurant;
use Dish\Form\DishForm;
use Zend\Session\Container;
use Zend\Log\Logger;

use Zend\File\Transfer\Adapter;
use Zend\View\Model\JsonModel;

class DishController extends AbstractActionController
{
    protected $dishTable;
    protected $dishgroupTable;
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
            $groupid = $this->params()->fromQuery('groupid',0);
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
                'dishes' => $this->getDishTable()->getDishes($rid, $groupid),
                'restaurants' => $restaurants,
                'current_restaurant' => $current_restaurant,
                'dishgroups' => $this->getDishgroupTable()->getDishgroups($rid),
            ));
        }
        else
        {
        
            return $this->redirect()->toRoute('adminhome');
        }         
    }

    public function addAction()
    {
        $rid = $this->session->restaurantID;
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    
        $form = new DishForm($dbAdapter,$rid);
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setAttribute('value', $rid);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dish = new Dish();
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $dish->exchangeArray($form->getData());
                $this->getDishTable()->saveDish($dish);
    
                return $this->redirect()->toRoute('admindish');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admindish', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $dish = $this->getDishTable()->getDish($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('admindish', array(
                'action' => 'index'
            ));
        }
    
        $rid = $this->session->restaurantID;
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new DishForm($dbAdapter,$rid);
        $form->bind($dish);
        $form->get('submit')->setAttribute('value', 'Edit');
        $form->get('rid')->setAttribute('value', $rid);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getDishTable()->saveDish($dish);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('admindish');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
            'dish' => $this->getDishTable()->getDish($dish->id),
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admindish');
        }
    
        $this->getDishTable()->deleteDish($id);
        return $this->redirect()->toRoute('admindish');
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
    
    public function getDishgroupTable()
    {
        if (!$this->dishgroupTable) {
            $sm = $this->getServiceLocator();
            $this->dishgroupTable = $sm->get('Dish\Model\DishgroupTable');
        }
        return $this->dishgroupTable;
    }
        
    public function getDishTable()
    {
        if (!$this->dishTable) {
            $sm = $this->getServiceLocator();
            $this->dishTable = $sm->get('Dish\Model\DishTable');
        }
        return $this->dishTable;
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