<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Auth\Form\LoginForm;
use Auth\Form\Filter\LoginFilter;

use Zend\Session\Container;
use Zend\Log\Logger;

use Customer\Model\Customer;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;
/**
 * ProfileController
 *
 * @author
 *
 * @version
 *
 */
class ProfileController extends AbstractActionController
{

    private $customerTable;
    
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
            
            $id = $this->session->customerID;
            $customer = $this->getCustomerTable()->getCustomer($id);
            
            $view = new ViewModel(array(
                'customer' => $customer,
                
            ));
    
            return $view;
        }
        else 
        {
            return $this->redirect()->toRoute('customerhome');
        }
    }

    public function changeAction()
    {
        //$this->getLogger()->log(Logger::INFO, $this->session->offsetExists('customerID'));
        if ($this->session->offsetExists('customerID')) {
    
            $id = $this->session->customerID;
            $customer = $this->getCustomerTable()->getCustomer($id);
    
            $view = new ViewModel(array(
                'customer' => $customer,
    
            ));
    
            return $view;
        }
    }
    
    public function uploadAction()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeaders($headers);
        $request = $this->getRequest();
        
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        Json::$useBuiltinEncoderDecoder = true;
                
        $valid = false;
        $name = "";
        $url = "";
        $message = "";
    
        if ($this->getRequest()->isPost())
        {
            $data = $request->getPost();
            $cid=$data['cid'];
            if($cid == 0)
            {
                $data = json_decode($this->getRequest()->getContent(),true);
                $cid = $data["cid"];
            }
            $imgsrc = $data["imgsrc"];
            
            $destination = sprintf('./data/data/customer/%09d/customer.jpeg',$cid);
            
            file_put_contents($destination, base64_decode($imgsrc));
    
            $data = array(  
                'valid'=>$valid
            );
    
            $result = new JsonModel($data);
    
            return $result;
        }
    }
    
    
    public function pushnotificationAction()
    {
        //$this->getLogger()->log(Logger::INFO, $this->session->offsetExists('customerID'));
        if ($this->session->offsetExists('customerID')) {
    
            $id = $this->session->customerID;
            $customer = $this->getCustomerTable()->getCustomer($id);
    
            $view = new ViewModel(array(
                'customer' => $customer,
    
            ));
    
            return $view;
        }
    }
    
    public function getCustomerTable()
    {
        if (!$this->customerTable) {
            $sm = $this->getServiceLocator();
            $this->customerTable = $sm->get('Customer\Model\CustomerTable');
        }
        return $this->customerTable;
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