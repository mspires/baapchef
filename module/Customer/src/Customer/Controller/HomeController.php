<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Log\Logger;
use Dish\Model\TakeoutDish;

class HomeController extends AbstractActionController
{
    protected $takeoutdishTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
        
    }
    
    public function indexAction()
    {
        if (!$this->session->offsetExists('customerID')) {
        
            return new ViewModel();
        
        }
        else 
        {
            /*
            $view = new ViewModel(array(
                'customerID' => $this->session->customerID,
                'dishes' => $this->getTakeoutDishTable()->fetchAll()
            ));
            $view->setTemplate('Customer/takeout-dish/index');
            
            return $view;
            */
            return $this->redirect()->toRoute('mytakeoutdish');
        }

        /*
        
        
        $article = 'The Mystery of Capitalism';
            
        $view = new ViewModel();
        $view->setTemplate('Customer/home/me');
        
        
        $dishView = new ViewModel(array('dishes' => $this->getTakeoutDishTable()->getDishes()));
        $dishView->setTemplate('Customer/takeout-dish/index');
        
        
        $primarySidebarView = new ViewModel();
        $primarySidebarView->setTemplate('Customer/main-sidebar');

        $secondarySidebarView = new ViewModel();
        $secondarySidebarView->setTemplate('Customer/secondary-sidebar');

        $sidebarBlockView = new ViewModel();
        $sidebarBlockView->setTemplate('Customer/block');

        $secondarySidebarView->addChild($sidebarBlockView, 'block');

        $view->addChild($dishView, 'article')
             ->addChild($primarySidebarView, 'sidebar_primary')
             ->addChild($secondarySidebarView, 'sidebar_secondary');
        
        return $view;
        */
    }
    
    public function getTakeoutDishTable()
    {
        if (!$this->takeoutdishTable) {
            $sm = $this->getServiceLocator();
            $this->takeoutdishTable = $sm->get('Dish\Model\TakeoutDishTable');
        }
        return $this->takeoutdishTable;
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