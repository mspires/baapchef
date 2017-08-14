<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Order for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Order\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Order\Model\OrderItem;
use Order\Form\OrderItemForm;

class OrderitemController extends AbstractActionController
{
    protected $orderItemTable;
    
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'orderitems' => $this->getOrderItemTable()->fetchAll(),
        ));   
    }
    
    public function addAction()
    {
        $form = new OrderItemForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $orderitem = new OrderItem();
            $form->setInputFilter($orderitem->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $orderitem->exchangeArray($form->getData());
                $this->getOrderItemTable()->saveOrder($orderitem);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('orderitem');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('orderitem', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $orderitem = $this->getOrderItemTable()->getOrder($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('orderitem', array(
                'action' => 'index'
            ));
        }
    
        $form  = new OrderItemForm();
        $form->bind($orderitem);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($orderitem->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getOrderItemTable()->saveOrderItem($orderitem);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('orderitem');
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
            return $this->redirect()->toRoute('orderitem');
        }
    
        $this->getOrderTable()->deleteOrder($id);
        return $this->redirect()->toRoute('orderitem');
    }
        
    public function getOrderItemTable()
    {
        if (!$this->orderItemTable) {
            $sm = $this->getServiceLocator();
            $this->orderItemTable = $sm->get('Order\Model\OrderItemTable');
        }
        return $this->orderItemTable;
    }
}
