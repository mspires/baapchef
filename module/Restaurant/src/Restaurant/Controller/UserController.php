<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\User;
use User\Form\UserForm;
use Zend\Session\Container;

class UserController extends AbstractActionController
{
    protected $userTable;
    
    const USER_RESTAURANT     = 'Restaurant';
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
        
            $rid = $this->session->restaurantID;
        
            return new ViewModel(array(
                'users' => $this->getUserTable()->getUseres(User::USER_RESTAURANT,$rid),
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
        
        $form = new UserForm($dbAdapter);
        $form->get('submit')->setValue('Add');
        $form->get('usertype')->setValue(User::USER_RESTAURANT);
        $form->get('rid')->setValue($rid);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $this->getUserTable()->saveUser($user);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('restuser');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('restuser', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $user = $this->getUserTable()->getUser($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('restuser', array(
                'action' => 'index'
            ));
        }
    
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $form  = new UserForm($dbAdapter);
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Edit');
        $form->get('role_id')->setValue($user->role_id);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getUserTable()->saveUser($user);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('restuser');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('restuser');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getUserTable()->deleteUser($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('restuser');
        }
    
        return array(
            'id'    => $id,
            'album' => $this->getUserTable()->getUser($id)
        );
    }
        
    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
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
