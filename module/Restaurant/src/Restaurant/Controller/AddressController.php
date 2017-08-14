<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Restaurant\Model\Address;
use Restaurant\Form\AddressForm;
use Zend\Session\Container;

class AddressController extends AbstractActionController
{
    protected $addressTable;
    protected $session;
    /**
     * The default action - show the home page
     */
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
            
            $id = $this->session->restaurantID;
            return new ViewModel(array(
                'addresses' => $this->getAddressTable()->getAddresses('restaurant', $id),
            ));   
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        }
}
    
    public function addAction()
    {
        $form = new AddressForm();
        $form->get('submit')->setValue('Add');
        $form->get('type')->setValue('restaurant');
        $form->get('tid')->setValue($this->session->restaurantID);
        $form->get('country')->setValue('US');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $address = new Address();
            $form->setInputFilter($address->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $address->exchangeArray($form->getData());
                $this->getAddressTable()->saveAddress($address);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('restaurant');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('address', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $address = $this->getAddressTable()->getAddress($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('address', array(
                'action' => 'index'
            ));
        }
    
        $form  = new AddressForm();
        $form->bind($address);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($address->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getAddressTable()->saveAddress($address);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('address');
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
            return $this->redirect()->toRoute('address');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAddressTable()->deleteAddress($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('address');
        }
    
        return array(
            'id'    => $id,
            'address' => $this->getAddressTable()->getAddress($id)
        );
    }
        
    public function getAddressTable()
    {
        if (!$this->addressTable) {
            $sm = $this->getServiceLocator();
            $this->addressTable = $sm->get('Restaurant\Model\AddressTable');
        }
        return $this->addressTable;
    }
}
