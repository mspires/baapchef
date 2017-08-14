<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cart\Model\CartItem;
use Cart\Form\cartitemForm;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Zend\Session\Container;
use Zend\Log\Logger;

class CartItemController extends AbstractActionController
{
    protected $cartitemTable;
    protected  $dishTable;
    
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
            
            return $view->setVariables(array(
                'id' => $id,
                'cartitems' => $this->getCartItemTable()->getCartItems(1),
            )); 
        }
        else
        {
            return $this->redirect()->toRoute('customerhome');
        }
    }

    public function addAction()
    {
        $form = new CartItemForm();
        $form->get('submit')->setValue('Add');
        $form->get('dishid')->setValue(1);
        $form->get('cartid')->setValue(1);
        $form->get('qty')->setValue(1);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cartitem = new CartItem();
            $form->setInputFilter($cartitem->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $cartitem->exchangeArray($form->getData());
                $this->getCartItemTable()->saveCartItem($cartitem);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('mycartitem');
            }
        }
        return array('form' => $form, 'dish' => $this->getDishTable()->getDish($dishid));
    }
    
    public function additemAction()
    {
        $dishid = (int) $this->params()->fromRoute('id', 0);
        if (!$dishid) {
            return $this->redirect()->toRoute('mydish');
        }
                
        $form = new CartItemForm();
        $form->get('submit')->setValue('Add');
        $form->get('dishid')->setValue($dishid);
        $form->get('cartid')->setValue(1);
        $form->get('qty')->setValue(1);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cartitem = new CartItem();
            $form->setInputFilter($cart->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $cart->exchangeArray($form->getData());
                $this->getCartItemTable()->saveCartItem($cartitem);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('mycartitem');
            }
        }
        return array('form' => $form, 'dish' => $this->getDishTable()->getDish($dishid));
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('mydish');
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $cartitem = $this->getCartItemTable()->getCartItem($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('mycartitem', array(
                'action' => 'index'
            ));
        }
    
        $form  = new CartItemForm();
        $form->bind($cartitem);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($cartitem->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getCartItemTable()->saveCartItem($cartitem);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('mycartitem');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
            'dish' => $this->getDishTable()->getDish($cartitem->dishid)
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cartitem');
        }
    
        $this->getCartItemTable()->deleteCart($id);
        return $this->redirect()->toRoute('mycartitem');
    }

    public function getCartItemTable()
    {
        if (!$this->cartitemTable) {
            $sm = $this->getServiceLocator();
            $this->cartitemTable = $sm->get('Cart\Model\CartItemTable');
        }
        return $this->cartitemTable;
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