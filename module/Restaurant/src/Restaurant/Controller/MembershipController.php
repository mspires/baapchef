<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Membership\Model\Membership;
use Zend\Session\Container;

class MembershipController extends AbstractActionController
{
    protected $membershipTable;
    protected $session;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
        
            $rid = $this->session->restaurantID;
            return new ViewModel(array(
                'memberships' => $this->getMembershipTable()->getMembershipsFromRestaurant($rid),
            )); 
        }
        else 
        {
            return $this->redirect()->toRoute('restauranthome');
        }
    }
    
    public function getMembershipTable()
    {
        if (!$this->membershipTable) {
            $sm = $this->getServiceLocator();
            $this->membershipTable = $sm->get('Membership\Model\MembershipTable');
        }
        return $this->membershipTable;
    }
}