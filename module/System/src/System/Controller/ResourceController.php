<?php
namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * ResourceController
 *
 * @author
 *
 * @version
 *
 */
use Resource\Model\Resource;
use Resource\Form\ResourceForm;

class ResourceController extends AbstractActionController
{
    protected $resourceTable;
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'resources' => $this->getResourceTable()->fetchAll(),
        ));   
    }
    
    public function addAction()
    {
        $form = new ResourceForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $resource = new Resource();
            $form->setInputFilter($resource->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $resource->exchangeArray($form->getData());
                $this->getResourceTable()->saveResource($resource);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('resource');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('resource', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
           $resource = $this->getResourceTable()->getResource($id);
        }
        catch (\Exception $ex) {
           return $this->redirect()->toRoute('resource', array(
               'action' => 'index'
          ));
        }
    
        $form  = new ResourceForm();
        $form->bind($resource);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($resource->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getResourceTable()->saveResource($resource);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('resource');
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
            return $this->redirect()->toRoute('resource');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteResource($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('resource');
        }
    
        return array(
            'id'    => $id,
            'resource' => $this->getResourceTable()->getResource($id)
        );
    }

    public function getResourceTable()
    {
        if (!$this->resourceTable) {
            $sm = $this->getServiceLocator();
            $this->resourceTable = $sm->get('System\Model\ResourceTable');
        }
        return $this->resourceTable;
    }

}