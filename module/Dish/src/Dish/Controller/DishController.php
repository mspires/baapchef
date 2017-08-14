<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Dish for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Dish\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\Dish;
use Dish\Form\DishForm;

class DishController extends AbstractActionController
{
    protected $dishTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'dishes' => $this->getDishTable()->fetchAll(),
        ));        
    }

     public function addAction()
    {
        $form = new DishForm();
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
        
        $form  = new DishForm();
        $form->bind($dish);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $this->getDishTable()->saveDish($dish);
        
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
                $this->getDishTable()->deleteDish($id);
            }
        
            // Redirect to list of albums
            return $this->redirect()->toRoute('dish');
        }
        
        return array(
            'id'    => $id,
            'dish' => $this->getDishTable()->getDish($id)
        );        
    }
     
    public function getDishTable()
    {
        if (!$this->dishTable) {
            $sm = $this->getServiceLocator();
            $this->dishTable = $sm->get('Dish\Model\DishTable');
        }
        return $this->dishTable;
    }
}
