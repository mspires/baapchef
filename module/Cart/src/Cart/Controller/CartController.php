<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Cart for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Cart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cart\Model\Cart;
use Cart\Form\CartForm;

class CartController extends AbstractActionController
{
    protected $cartTable;
    
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'carts' => $this->getCartTable()->fetchAll(),
        ));   
    }
    
    public function addAction()
    {
        $form = new CartForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cart = new Cart();
            $form->setInputFilter($cart->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $cart->exchangeArray($form->getData());
                $this->getCartTable()->saveCart($cart);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('cart');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cart', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $cart = $this->getCartTable()->getCart($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('cart', array(
                'action' => 'index'
            ));
        }
    
        $form  = new CartForm();
        $form->bind($cart);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($cart->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getCartTable()->saveCart($cart);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('cart');
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
            return $this->redirect()->toRoute('cart');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getCartTable()->deleteCart($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('cart');
        }
    
        return array(
            'id'    => $id,
            'album' => $this->getCartTable()->getCart($id)
        );
    }
        
    public function getCartTable()
    {
        if (!$this->cartTable) {
            $sm = $this->getServiceLocator();
            $this->cartTable = $sm->get('Cart\Model\CartTable');
        }
        return $this->cartTable;
    }
}

