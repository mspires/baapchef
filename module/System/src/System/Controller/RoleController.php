<?php
namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * RoleController
 *
 * @author
 *
 * @version
 *
 */
use Role\Model\Role;
use Role\Form\RoleForm;

class RoleController extends AbstractActionController
{
    protected $roleTable;
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'roles' => $this->getRoleTable()->fetchAll(),
        ));   
    }
    
    public function addAction()
    {
        $form = new RoleForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $role = new Role();
            $form->setInputFilter($role->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $role->exchangeArray($form->getData());
                $this->getRoleTable()->saveRole($role);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('role');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('role', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
           $role = $this->getRoleTable()->getRole($id);
        }
        catch (\Exception $ex) {
           return $this->redirect()->toRoute('role', array(
               'action' => 'index'
          ));
        }
    
        $form  = new RoleForm();
        $form->bind($role);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($role->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getRoleTable()->saveRole($role);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('role');
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
            return $this->redirect()->toRoute('role');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteRole($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('role');
        }
    
        return array(
            'id'    => $id,
            'role' => $this->getRoleTable()->getRole($id)
        );
    }

    public function getRoleTable()
    {
        if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('System\Model\RoleTable');
        }
        return $this->roleTable;
    }

}