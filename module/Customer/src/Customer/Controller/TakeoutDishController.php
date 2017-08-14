<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\TakeoutDish;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;

use Zend\Session\Container;
use Zend\Log\Logger;

class TakeoutDishController extends AbstractActionController
{
    protected $takeoutdishTable;
    
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
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        
        Json::$useBuiltinEncoderDecoder = true;
        
        $price = $this->params()->fromQuery('price','8.00');
        
        $query = 'price=' . $price;
        
        $dishes = $this->getTakeoutDishTable()->getDishesByQuery($query);
        
        return $view->setVariables(array(
            'customerID' => $this->session->customerID,
            'dishes' => $dishes,            
        ));
        
    }

    public function takeoutAction()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeaders($headers);
        $request = $this->getRequest();
        
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        Json::$useBuiltinEncoderDecoder = true;
    
        $query="";
        //$price = $this->params()->fromQuery('price','8.00');
        //$query = 'price=' . $price;
    
        $dishes = $this->getTakeoutDishTable()->getDishesByQuery($query);
    
        return $view->setVariables(array(
            'customerID' => $this->session->customerID,
            'dishes' => $dishes->toArray(),
        ));
    
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