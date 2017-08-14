<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class PaymentController extends AbstractActionController
{
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
            
            $id = $this->session->restaurantID;
            return new ViewModel();
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        } 
    }
}