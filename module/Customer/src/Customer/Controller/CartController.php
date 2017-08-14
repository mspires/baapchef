<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cart\Model\Cart;
use Cart\Model\CartItem;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Zend\Session\Container;
use Zend\Log\Logger;


class CartController extends AbstractActionController
{
    protected $cartTable;
    protected $cartItemTable;

    protected $session;
    protected $logger;
    
    private $acceptCriteria = array(
        'Zend\View\Model\ViewModel' => array(
            'text/html',
        ),
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        ));
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('customerID')) {
        
            $view = $this->acceptableViewModelSelector($this->acceptCriteria);
            
            Json::$useBuiltinEncoderDecoder = true;
            
            $id = $this->session->customerID;
            
            $carts = $this->getCartTable()->getCarts($id);       
            //$cartitems = $this->getCartItemTable()->getCartItems(1);            
             
            return $view->setVariables(array(
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