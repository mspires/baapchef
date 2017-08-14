<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\User;
use User\Form\UserForm;
use Zend\Session\Container;
use Zend\Log\Logger;

class UserController extends AbstractActionController
{
    protected $userTable;
    
    const DEFAULT_USER_ROLR_MEMBER   = 1;

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
                'users' => $this->getUserTable()->getUseres(User::USER_ADMIN, User::DEFAULT_USER_ADMIN_ID),
            ));
        }
        else
        {
        
            return $this->redirect()->toRoute('adminhome');
        }           
    }
    
    public function addAction()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $form = new UserForm($dbAdapter);
        $form->get('submit')->setValue('Add');
        $form->get('usertype')->setValue(User::USER_ADMIN);
        $form->get('rid')->setValue(User::DEFAULT_USER_ADMIN_ID);
        $form->get('role_id')->setValue(UserController::DEFAULT_USER_ROLR_MEMBER);
        $form->get('status')->setValue('Y');
            
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $this->getUserTable()->saveUser($user);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('adminuser');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminuser', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $user = $this->getUserTable()->getUser($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('adminuser', array(
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
                return $this->redirect()->toRoute('adminuser');
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
            return $this->redirect()->toRoute('adminuser');
        }
    
        $this->getUserTable()->deleteUser($id);
        return $this->redirect()->toRoute('adminuser');
    }
        
    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
}
