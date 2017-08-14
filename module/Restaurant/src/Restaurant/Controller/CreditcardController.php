<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Creditcard\Model\Creditcard;
use Creditcard\Form\CreditcardForm;
use Zend\Session\Container;

class CreditcardController extends AbstractActionController
{
    protected $creditcardTable;
    protected $session;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
        
            $id = $this->session->restaurantID;
            return new ViewModel(array(
                'creditcards' => $this->getCreditcardTable()->getCreditcards('restaurant',$id),
            )); 
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        }
    }
    
    public function addAction()
    {
        $form = new CreditcardForm();
        $form->get('submit')->setValue('Add');
        $form->get('tid')->setValue(1);
        $form->get('type')->setValue('restaurant');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $creditcard = new Creditcard();
            $form->setInputFilter($creditcard->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $creditcard->exchangeArray($form->getData());
                $this->getCreditcardTable()->saveCreditcard($creditcard);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('creditcard');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('restcredit', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $creditcard = $this->getCreditcardTable()->getCreditcard($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('restcredit', array(
                'action' => 'index'
            ));
        }
    
        $form  = new CreditcardForm();
        $form->bind($creditcard);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($creditcard->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getCreditcardTable()->saveCreditcard($creditcard);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('restcredit');
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
            return $this->redirect()->toRoute('restcredit');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getCreditcardTable()->deleteCreditcard($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('restcredit');
        }
    
        return array(
            'id'    => $id,
            'album' => $this->getCreditcardTable()->getCreditcard($id)
        );
    }
    
    public function getCreditcardTable()
    {
        if (!$this->creditcardTable) {
            $sm = $this->getServiceLocator();
            $this->creditcardTable = $sm->get('Creditcard\Model\CreditcardTable');
        }
        return $this->creditcardTable;
    }
}