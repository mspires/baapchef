<?php
namespace Agent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Restaurant\Model\Address;
use Restaurant\Form\AddressForm;
use Zend\Session\Container;

/**
 * AddressController
 *
 * @author
 *
 * @version
 *
 */
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
        return new ViewModel(array(
            'addresses' => $this->getAddressTable()->fetchAll(),
        ));   
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
                return $this->redirect()->toRoute('agrestaddress');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('agrestaddress', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $address = $this->getAddressTable()->getAddress($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('agrestaddress', array(
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
                return $this->redirect()->toRoute('agrestaddress');
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
            return $this->redirect()->toRoute('agrestaddress');
        }
    
        $this->getAddressTable()->deleteAddress($id);
        return $this->redirect()->toRoute('agrestaddress');
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
