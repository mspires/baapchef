<?php
/**
 * DishgroupgroupController
 *
 * @author
 *
 * @version
 *
 */
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\Dishgroup;
use Dish\Form\DishgroupForm;
use Zend\Session\Container;
use Zend\Log\Logger;

class DishgroupController extends AbstractActionController
{
    protected $dishgroupTable;
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
                'dishgroups' => $this->getDishgroupTable()->getDishgroups($rid),
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
        
        $form = new DishgroupForm();
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setValue($rid);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dishgroup = new Dishgroup();
            $form->setInputFilter($dishgroup->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $dishgroup->exchangeArray($form->getData());
                $this->getDishgroupTable()->saveDishgroup($dishgroup);

                // Redirect to list of albums
                return $this->redirect()->toRoute('restdishgroup');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('restdishgroup', array(
                'action' => 'add'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $dishgroup = $this->getDishgroupTable()->getDishgroup($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('restdishgroup', array(
                'action' => 'index'
            ));
        }

        $form  = new DishgroupForm();
        $form->bind($dishgroup);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dishgroup->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getDishgroupTable()->saveDishgroup($dishgroup);

                // Redirect to list of albums
                return $this->redirect()->toRoute('restdishgroup');
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
            return $this->redirect()->toRoute('restdishgroup');
        }

        $this->getDishgroupTable()->deleteDishgroup($id);
        return $this->redirect()->toRoute('restdishgroup');
    }
     
    public function getDishgroupTable()
    {
        if (!$this->dishgroupTable) {
            $sm = $this->getServiceLocator();
            $this->dishgroupTable = $sm->get('Dish\Model\DishgroupTable');
        }
        return $this->dishgroupTable;
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
