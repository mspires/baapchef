<?php
namespace Dish\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\TakeoutDish;
use Dish\Form\TakeoutDishForm;
use Zend\Session\Container;
/**
 * TakeoutDishController
 *
 * @author
 *
 * @version
 *
 */
class TakeoutDishController extends AbstractActionController
{

    protected $session;
    /**
     * The default action - show the home page
     */
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    protected $takeoutdishTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'dishes' => $this->getTakeoutDishTable()->fetchAll(),
        ));        
    }

     public function addAction()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $form = new TakeoutDishForm($dbAdapter,$this->session->restaurantID);
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $dish = new Dish();
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $dish->exchangeArray($form->getData());
                $this->getDishTable()->saveDish($dish);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('dish');
            }
        }
        return array('form' => $form);        
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dish', array(
                'action' => 'add'
            ));
        }
        
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $dish = $this->getDishTable()->getDish($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('dish', array(
                'action' => 'index'
            ));
        }
        
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $form = new TakeoutDishForm($dbAdapter,$this->session->restaurantID);
        
        $form->bind($dish);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $this->getTakeoutDishTable()->saveDish($dish);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('dish');
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
            return $this->redirect()->toRoute('dish');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
        
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getTakeoutDishTable()->deleteDish($id);
            }
        
            // Redirect to list of albums
            return $this->redirect()->toRoute('dish');
        }
        
        return array(
            'id'    => $id,
            'dish' => $this->getTakeoutDishTable()->getDish($id)
        );        
    }
     
    public function getTakeoutDishTable()
    {
        if (!$this->takeoutdishTable) {
            $sm = $this->getServiceLocator();
            $this->takeoutdishTable = $sm->get('Dish\Model\TakeoutDishTable');
        }
        return $this->takeoutdishTable;
    }
}