<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Event for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Event\Form\EventForm;
use Event\Form\Filter\EventFilter;
use Event\Model\Event;

use Zend\Session\Container;
use Zend\Log\Logger;
use Admin\Model\SendEmail;

class EventController extends AbstractActionController
{
    private  $eventTable;

    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        return new ViewModel(array(
            'events' => $this->getEventTable()->fetchAll(),
        ));
    }
    
    public function addAction()
    {
        $form = new EventForm();
        $form->setInputFilter(new EventFilter());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                //print_r($form->getData());
                $event = new Event();
                $event->exchangeArray($form->getData());
                $this->getEventTable()->saveEvent($event);
                
                $email = new SendEmail($this);
                $email->sendevent($event);
                
                return $this->redirect()->toRoute('adminevent');
            }
        }
        return new ViewModel(array('form' => $form));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminevent', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $event = $this->getEventTable()->getEvent($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('adminevent', array(
                'action' => 'index'
            ));
        }
    
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new EventForm();
        $form->bind($event);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dish->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getEventTable()->saveEvent($event);
    
                return $this->redirect()->toRoute('adminevent');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
            'dish' => $this->getEventTable()->getEvent($event->id),
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminevent');
        }
    
        $this->getEventTable()->deleteEvent($id);
        return $this->redirect()->toRoute('adminevent');
    }
        
    public function getEventTable()
    {
        if (!$this->eventTable) {
            $sm = $this->getServiceLocator();
            $this->eventTable = $sm->get('Event\Model\EventTable');
        }
        return $this->eventTable;
    }
}
