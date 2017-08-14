<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Waiting for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Waiting\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Waiting\Form\WaitingForm;
use Waiting\Model\Waiting;

class WaitingController extends AbstractActionController
{
    private  $waitingTable;

    public function indexAction()
    {
        $rid=10000;
        return new ViewModel(array(
            'waitings' => $this->getWaitingTable()->getWaitings($rid),
        ));
    }

    public function addAction()
    {
        $rid = 10000;

        $form = new WaitingForm();
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setValue($rid);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $waiting = new Waiting();
            $form->setInputFilter($waiting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $waiting->exchangeArray($form->getData());
                $this->getWaitingTable()->saveWaiting($waiting);

                // Redirect to list of albums
                return $this->redirect()->toRoute('waiting');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('waiting', array(
                'action' => 'add'
            ));
        }

        try {
            $waiting = $this->getWaitingTable()->getWaiting($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('waiting', array(
                'action' => 'index'
            ));
        }

        $form = new WaitingForm();
        $form->bind($waiting);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($waiting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getWaitingTable()->saveWaiting($waiting);

                // Redirect to list of albums
                return $this->redirect()->toRoute('waiting');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'waiting' => $this->getWaitingTable()->getWaiting($waiting->id),
        );
    }

    public function listAction()
    {
        return new ViewModel(array(
            'waitings' => $this->getWaitingTable()->getWaitings(),
        ));
    }

    public function getWaitingTable()
    {
        if (!$this->waitingTable) {
            $sm = $this->getServiceLocator();
            $this->waitingTable = $sm->get('Waiting\Model\WaitingTable');
        }
        return $this->waitingTable;
    }
}
