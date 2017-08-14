<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\TakeoutDish;
use Dish\Form\TakeoutDishForm;
use Zend\Session\Container;

use Zend\File\Transfer\Adapter;
use Zend\View\Model\JsonModel;

class TakeoutDishController extends AbstractActionController
{
    protected $takeoutdishTable;
    
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
            
            $rid = $this->session->restaurantID;
            
            return new ViewModel(array(
                'dishes' => $this->getTakeoutDishTable()->getDishes($rid),
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
        
        $form = new TakeoutDishForm();
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setValue($rid);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dish = new TakeoutDish();
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $dish->exchangeArray($form->getData());
                $this->getTakeoutDishTable()->saveDish($dish);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('resttakeoutdish');
            }
        }
        return array('form' => $form);        
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('resttakeoutdish', array(
                'action' => 'add'
            ));
        }
        
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $dish = $this->getTakeoutDishTable()->getDish($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('resttakeoutdish', array(
                'action' => 'index'
            ));
        }
        
        $rid = $this->session->restaurantID;
        
        $form = new TakeoutDishForm();
        $form->get('rid')->setValue($rid);
        
        $form->bind($dish);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $this->getTakeoutDishTable()->saveDish($dish);

                return $this->redirect()->toRoute('resttakeoutdish');
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
            return $this->redirect()->toRoute('resttakeoutdish');
        }
        
        $this->getTakeoutDishTable()->deleteDish($id);
        return $this->redirect()->toRoute('resttakeoutdish');
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
    
    public function getTakeoutDishTable()
    {
        if (!$this->takeoutdishTable) {
            $sm = $this->getServiceLocator();
            $this->takeoutdishTable = $sm->get('Dish\Model\TakeoutDishTable');
        }
        return $this->takeoutdishTable;
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