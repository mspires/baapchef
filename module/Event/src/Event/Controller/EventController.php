<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Event for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Event\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Event\Form\EventForm;
use Event\Form\Filter\EventFilter;
use Event\Model\Event;

class EventController extends AbstractActionController
{
    private  $eventTable;
    
    public function indexAction()
    {
        $form = new EventForm();
        $form->setInputFilter(new EventFilter());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                print_r($form->getData());
                $event = new Event();
                $event->exchangeArray($form->getData());
                $this->getEventTable()->saveEvent($event);
                
                return $this->redirect()->toRoute('event',array('action'=>'list'));
            }
        }
        return new ViewModel(array('form' => $form));
    }
    
    public function listAction()
    {
        return new ViewModel(array(
            'events' => $this->getEventTable()->fetchAll(),
        ));
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
