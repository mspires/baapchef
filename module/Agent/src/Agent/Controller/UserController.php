<?php
namespace Agent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
/**
 * UserController
 *
 * @author
 *
 * @version
 *
 */
use Zend\View\Model\ViewModel;
use User\Model\User;
use User\Form\UserForm;

class UserController extends AbstractActionController
{
    protected $userTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('agentID')) {

            $id = $this->session->agentID;
            
            return new ViewModel(array(
                'users' => $this->getUserTable()->getUseres(User::USER_AGENT,$id),
            ));   
        }   
        else
        {
        
            return $this->redirect()->toRoute('agenthome');
        }             
    }
    
    public function addAction()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $form = new UserForm($dbAdapter);
        $form->get('submit')->setValue('Add');
        $form->get('usertype')->setValue(User::USER_AGENT);
        $form->get('rid')->setValue($this->session->agentID);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $this->getUserTable()->saveUser($user);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('aguser');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('aguser', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $user = $this->getUserTable()->getUser($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('aguser', array(
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
                return $this->redirect()->toRoute('aguser');
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
            return $this->redirect()->toRoute('aguser');
        }
    
        $this->getUserTable()->deleteUser($id);
        return $this->redirect()->toRoute('aguser');
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
