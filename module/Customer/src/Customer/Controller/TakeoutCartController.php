<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cart\Model\Cart;
use Cart\Model\CartItem;

/**
 * TakeoutCartController
 *
 * @author
 *
 * @version
 *
 */
class TakeoutCartController extends AbstractActionController
{
    protected $cartTable;
    protected $cartItemTable;
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        if ($this->session->offsetExists('customerID')) {
        
            $id = $this->session->customerID;
        
            $carts = $this->getCartTable()->getCarts($id);
            //$cartitems = $this->getCartItemTable()->getCartItems(1);
             
            return new ViewModel(array(
                'carts' => $carts,
                //'cartitems' => $cartitems,
            ));

        }
        else
        {
            return $this->redirect()->toRoute('customerhome');
        }                
    }
    
    public function getCartTable()
    {
        if (!$this->cartTable) {
            $sm = $this->getServiceLocator();
            $this->cartTable = $sm->get('Cart\Model\CartTable');
        }
        return $this->cartTable;
    }
    
    public function getCartItemTable()
    {
        if (!$this->cartItemTable) {
            $sm = $this->getServiceLocator();
            $this->cartItemTable = $sm->get('Cart\Model\CartItemTable');
        }
        return $this->cartItemTable;
    }
}