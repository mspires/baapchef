<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Schedule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Schedule\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Schedule\Form\ScheduleForm;
use Schedule\Model\Schedule;

class ScheduleController extends AbstractActionController
{
    private  $scheduleTable;

    public function indexAction()
    {
        
        $rid=10000;
        return new ViewModel(array(
            'schedules' => $this->getScheduleTable()->getSchedules($rid),
        ));
    }

    public function addAction()
    {
        $rid = 10000;

        $form = new ScheduleForm();
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setValue($rid);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $schedule = new Schedule();
            $form->setInputFilter($schedule->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $schedule->exchangeArray($form->getData());
                $this->getScheduleTable()->saveSchedule($schedule);

                // Redirect to list of albums
                return $this->redirect()->toRoute('schedule');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('schedule', array(
                'action' => 'add'
            ));
        }

        try {
            $schedule = $this->getScheduleTable()->getSchedule($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('schedule', array(
                'action' => 'index'
            ));
        }

        $form = new ScheduleForm();
        $form->bind($schedule);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($schedule->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getScheduleTable()->saveSchedule($schedule);

                // Redirect to list of albums
                return $this->redirect()->toRoute('schedule');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'schedule' => $this->getScheduleTable()->getSchedule($schedule->id),
        );
    }

    public function listAction()
    {
        return new ViewModel(array(
            'schedules' => $this->getScheduleTable()->getSchedules(),
        ));
    }

    public function getScheduleTable()
    {
        if (!$this->scheduleTable) {
            $sm = $this->getServiceLocator();
            $this->scheduleTable = $sm->get('Schedule\Model\ScheduleTable');
        }
        return $this->scheduleTable;
    }
}