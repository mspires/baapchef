<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\Dish;
use Dish\Form\DishForm;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Restaurant\Model\Restaurant;

class DishController extends AbstractActionController
{
    protected $dishTable;
    protected $dishgroupTable;
    protected $restaurantTable;
    protected $userTable;
    
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
        if ($this->session->offsetExists('restaurantID')) {
            $groupid = $this->params()->fromQuery('groupid',0);
            $id = $this->session->restaurantID;
            return new ViewModel(array(
                'dishes' => $this->getDishTable()->getDishesByGroup($id, $groupid),
                'dishgroups' => $this->getDishgroupTable()->getDishgroups($id),
            ));
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        } 
    }
    
     public function addAction()
    {
        $rid = $this->session->restaurantID;
        
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new DishForm($dbAdapter,$rid);
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setValue($rid);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dish = new Dish();
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $dish->exchangeArray($form->getData());
                $this->getDishTable()->saveDish($dish);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('restdish');
            }
        }
        return array('form' => $form);        
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('restdish', array(
                'action' => 'add'
            ));
        }
        
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $dish = $this->getDishTable()->getDish($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('restdish', array(
                'action' => 'index'
            ));
        }
        
        $rid = $this->session->restaurantID;
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new DishForm($dbAdapter,$rid);
        $form->bind($dish);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $this->getDishTable()->saveDish($dish);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('restdish');
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
            return $this->redirect()->toRoute('restdish');
        }
        $this->getDishTable()->deleteDish($id);
        return $this->redirect()->toRoute('restdish');
    }
    
    public function menuAction()
    {
        $request = $this->getRequest();
        
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        
        Json::$useBuiltinEncoderDecoder = true;
        
        $id = $this->session->restaurantID;
        $userid = $this->session->userID;
        if ($request->isPost()) {
        
            $data = $request->getPost();
        
            $view->setVariables(array(
                'restaurant' => $this->getRestaurantTable()->getRestaurant($id),
                'user' => $this->getUserTable()->getUser($userid),
                'dishes' => $this->getDishTable()->getDishes($id),
                'dishgroups' => $this->getDishgroupTable()->getDishgroups($id),
            ));
        }
        else 
        {
            $groupid = 0;
            
            $view->setVariables(array(
                'dishes' => $this->getDishTable()->getDishesByGroup($id, $groupid),
                'dishgroups' => $this->getDishgroupTable()->getDishgroups($id),
            ));
        }

        return $view;     
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
            $destination = sprintf('./public/data/%09d',$rid);
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
    
    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
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