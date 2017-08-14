<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Membership\Model\Membership;
use Zend\Session\Container;
use Zend\Log\Logger;


class MembershipController extends AbstractActionController
{
    protected $membershipTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');    
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('customerID')) {
            $id = $this->session->customerID;
             return new ViewModel(array(
                'memberships' => $this->getMembershipTable()->getMemberships($id),
            ));
        }
        else
        {
            return $this->redirect()->toRoute('customerhome');
        } 
    }
    
    public function addAction()
    {
        $id = $this->session->customerID;
        $rid = (int) $this->params()->fromRoute('id', 0);
        
        $membership = new Membership();
        
        $membership->rid = $rid;
        $membership->cid = $id;
        $membership->level = Membership::SILVER;
        $membership->status = Membership::MEMBERSHIP_PENDING;
        
        $this->getMembershipTable()->saveMembership($membership);

        return $this->redirect()->toRoute('mymembership');
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('mymembership');
        }
    
        $this->getMembershipTable()->deleteMembership($id);
        return $this->redirect()->toRoute('mymembership');
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