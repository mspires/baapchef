<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Reservation for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Reservation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Reservation\Form\ReservationForm;
use Reservation\Model\Reservation;

class ReservationController extends AbstractActionController
{
    private  $reserveTable;

    public function indexAction()
    {
        
        $rid=10000;
        return new ViewModel(array(
            'reserves' => $this->getReservationTable()->getReservations($rid),
        ));
    }

    public function addAction()
    {
        $rid = 10000;

        $form = new ReservationForm();
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setValue($rid);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $reserve = new Reservation();
            $form->setInputFilter($reserve->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $reserve->exchangeArray($form->getData());
                $this->getReservationTable()->saveReservation($reserve);

                // Redirect to list of albums
                return $this->redirect()->toRoute('reserve');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('reserve', array(
                'action' => 'add'
            ));
        }

        try {
            $reserve = $this->getReservationTable()->getReservation($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('reserve', array(
                'action' => 'index'
            ));
        }

        $form = new ReservationForm();
        $form->bind($reserve);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($reserve->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getReservationTable()->saveReservation($reserve);

                // Redirect to list of albums
                return $this->redirect()->toRoute('reserve');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'reserve' => $this->getReservationTable()->getReservation($reserve->id),
        );
    }

    public function listAction()
    {
        return new ViewModel(array(
            'reserves' => $this->getReservationTable()->getReservations(),
        ));
    }

    public function getReservationTable()
    {
        if (!$this->reserveTable) {
            $sm = $this->getServiceLocator();
            $this->reserveTable = $sm->get('Reservation\Model\ReservationTable');
        }
        return $this->reserveTable;
    }
}
