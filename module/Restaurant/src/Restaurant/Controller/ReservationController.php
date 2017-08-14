<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Table\Model\Table;
use Reservation\Model\Reservation;
use User\Model\User;
use Customer\Model\Customer;
use Zend\Session\Container;
use Zend\Log\Logger;
use MyLib\Controller\AppController;
use Zend\Json\Json;

/**
 * ReservationController
 *
 * @author
 *
 * @version
 *
 */
class ReservationController extends AbstractActionController
{
    protected $tableTable;
    protected $userTable;
    protected $customerTable;
    protected $reservationTable;
    
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
        if ($this->session->offsetExists('restaurantID')) {
    
            $request = $this->getRequest();
    
            if ($request->isPost()) {
    
                $data = $request->getPost();
    
                $view = $this->acceptableViewModelSelector($this->acceptCriteria);
                Json::$useBuiltinEncoderDecoder = true;
    
                $rid = $this->session->restaurantID;
    
                $view->setVariables(array(
                    'tables' => $this->getTableTable()->getTables($rid),
                    'users' => $this->getUserTable()->getUseres(User::USER_RESTAURANT,$rid),
                    'customers' => $this->getCustomerTable()->getCustomers($rid),
                    'reservations' => $this->getReservationTable()->getReservations($rid),
                ));
                $view->setTerminal(true);
    
                return $view;
            }
            else
            {
                $view = $this->acceptableViewModelSelector($this->acceptCriteria);
                Json::$useBuiltinEncoderDecoder = true;
    
                $rid = $this->session->restaurantID;
    
                $view->setVariables(array(
                    'tables' => $this->getTableTable()->getTables($rid),
                    'users' => $this->getUserTable()->getUseres(User::USER_RESTAURANT,$rid),
                    'reservations' => $this->getReservationTable()->getReservations($rid),
                ));
                $view->setTerminal(true);
    
                return $view;
            }
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        }
        
    }    
    
    public function getTableTable()
    {
        if (!$this->tableTable) {
            $sm = $this->getServiceLocator();
            $this->tableTable = $sm->get('Table\Model\TableTable');
        }
        return $this->tableTable;
    }
    
    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
    
    public function getCustomerTable()
    {
        if (!$this->customerTable) {
            $sm = $this->getServiceLocator();
            $this->customerTable = $sm->get('Customer\Model\CustomerTable');
        }
        return $this->customerTable;
    }
    
    public function getReservationTable()
    {
        if (!$this->reservationTable) {
            $sm = $this->getServiceLocator();
            $this->reservationTable = $sm->get('Reservation\Model\ReservationTable');
        }
        return $this->reservationTable;
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