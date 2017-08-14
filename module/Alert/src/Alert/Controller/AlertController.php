<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Alert for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Alert\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Alert\Form\AlertForm;
use Alert\Form\Filter\AlertFilter;
use Alert\Model\Alert;
 
class AlertController extends AbstractActionController
{
    private  $alertTable;
    
    public function indexAction()
    {
        
        $form = new AlertForm();
        $form->setInputFilter(new AlertFilter());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                //print_r($form->getData());
                $alert = new Alert();
                $alert->exchangeArray($form->getData());
                $this->getAlertTable()->saveAlert($alert);
                
                return $this->redirect()->toRoute('alert',array('action'=>'list'));
            }
        }
        return new ViewModel(array('form' => $form));
    }
    
    public function listAction()
    {
        return new ViewModel(array(
            'alerts' => $this->getAlertTable()->fetchAll(),
        ));
    }    
    
    public function getAlertTable()
    {
        if (!$this->alertTable) {
            $sm = $this->getServiceLocator();
            $this->alertTable = $sm->get('Alert\Model\AlertTable');
        }
        return $this->alertTable;
    }
}
