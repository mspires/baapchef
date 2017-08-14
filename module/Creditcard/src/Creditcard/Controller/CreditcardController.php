<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Creditcard for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Creditcard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Creditcard\Model\Creditcard;
use Creditcard\Form\CreditcardForm;
/**
 * CreditcardController
 *
 * @author
 *
 * @version
 *
 */
class CreditcardController extends AbstractActionController
{
    protected $creditcardTable;
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'creditcards' => $this->getCreditcardTable()->fetchAll(),
        )); 
    }
    
    public function addAction()
    {
        $form = new CreditcardForm();
        $form->get('submit')->setValue('Add');
    
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