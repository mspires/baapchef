<?php
namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * PermissionController
 *
 * @author
 *
 * @version
 *
 */
use Permission\Model\Permission;
use Permission\Form\PermissionForm;

class PermissionController extends AbstractActionController
{
    protected $permissionTable;
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'permissions' => $this->getPermissionTable()->fetchAll(),
        ));   
    }
    
    public function addAction()
    {
        $form = new PermissionForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $permission = new Permission();
            $form->setInputFilter($permission->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $permission->exchangeArray($form->getData());
                $this->getPermissionTable()->savePermission($permission);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('permission');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('permission', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
           $permission = $this->getPermissionTable()->getPermission($id);
        }
        catch (\Exception $ex) {
           return $this->redirect()->toRoute('permission', array(
               'action' => 'index'
          ));
        }
    
        $form  = new PermissionForm();
        $form->bind($permission);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($permission->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getPermissionTable()->savePermission($permission);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('permission');
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
            return $this->redirect()->toRoute('permission');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deletePermission($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('permission');
        }
    
        return array(
            'id'    => $id,
            'permission' => $this->getPermissionTable()->getPermission($id)
        );
    }

    public function getPermissionTable()
    {
        if (!$this->permissionTable) {
            $sm = $this->getServiceLocator();
            $this->permissionTable = $sm->get('System\Model\PermissionTable');
        }
        return $this->permissionTable;
    }

}