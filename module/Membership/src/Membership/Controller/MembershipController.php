<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Membership for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Membership\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Membership\Model\Membership;
use Membership\Form\MembershipForm;

class MembershipController extends AbstractActionController
{
    protected $membershipTable;
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'memberships' => $this->getMembershipTable()->fetchAll(),
        ));   
    }
    
    public function addAction()
    {
        $form = new MembershipForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $membership = new Membership();
            $form->setInputFilter($membership->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $membership->exchangeArray($form->getData());
                $this->getMembershipTable()->saveMembership($membership);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('membership');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('membership', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
           $membership = $this->getMembershipTable()->getMembership($id);
        }
        catch (\Exception $ex) {
           return $this->redirect()->toRoute('membership', array(
               'action' => 'index'
          ));
        }
    
        $form  = new MembershipForm();
        $form->bind($membership);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($membership->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getMembershipTable()->saveMembership($membership);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('membership');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('membership');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteMembership($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('membership');
        }
    
        return array(
            'id'    => $id,
            'album' => $this->getMembershipTable()->getMembership($id)
        );
    }

    public function getMembershipTable()
    {
        if (!$this->membershipTable) {
            $sm = $this->getServiceLocator();
            $this->membershipTable = $sm->get('Membership\Model\MembershipTable');
        }
        return $this->membershipTable;
    }
        
    public function getAddressTable()
    {
        if (!$this->addressTable) {
            $sm = $this->getServiceLocator();
            $this->addressTable = $sm->get('Membership\Model\AddressTable');
        }
        return $this->addressTable;
    }
}

