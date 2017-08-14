<?php
namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Table\Form\TableForm;
use Table\Form\Filter\TableFilter;
use Table\Model\Table;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

class TableController extends AbstractActionController
{
    protected $tableTable;

    
    protected $session;
    protected $logger;
    
    private $acceptCriteria = array(
        'Zend\View\Model\ViewModel' => array(
            'text/html',
        ),
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        ));
    
    public function __construct()
    {
        $this->session = new Container('User');
    }

    public function indexAction()
    {
        if ($this->session->offsetExists('restaurantID')) {   
                 
            $rid = $this->session->restaurantID;
            
            return new ViewModel(array(
                'tables' => $this->getTableTable()->getTables($rid),
            ));        
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        } 
    }

    public function addAction()
    {
        if ($this->session->offsetExists('restaurantID')) {   
                 
            $rid = $this->session->restaurantID;
        
            $form = new TableForm();
            $form->get('submit')->setValue('Add');
            $form->get('rid')->setValue($rid);
            
            $request = $this->getRequest();
            if ($request->isPost()) {
                $table = new Table();
                $form->setInputFilter(new TableFilter());
                $form->setData($request->getPost());
            
                if ($form->isValid()) {
                    $table->exchangeArray($form->getData());
                    $this->getTableTable()->saveTable($table);
            
                    // Redirect to list of albums
                    return $this->redirect()->toRoute('resttable');
                }
            }
            return array('form' => $form);    
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        }                
    }
    
    public function editAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
             
            $rid = $this->session->restaurantID;        
            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                return $this->redirect()->toRoute('resttable', array(
                    'action' => 'add'
                ));
            }
            
            try {
                $table = $this->getTableTable()->getTable($id);
            }
            catch (\Exception $ex) {
                return $this->redirect()->toRoute('resttable', array(
                    'action' => 'index'
                ));
            }
            
            $form = new TableForm();
            $form->bind($table);
            $form->get('submit')->setAttribute('value', 'Edit');
        
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setInputFilter(new TableFilter());
                $form->setData($request->getPost());
            
                if ($form->isValid()) {
                    $this->getTableTable()->saveTable($table);
            
                    // Redirect to list of albums
                    return $this->redirect()->toRoute('resttable');
                }
            }
            
            return array(
                'id' => $id,
                'form' => $form,
                'table' => $this->getTableTable()->getTable($table->id),
            );
            
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        }            
    }

    public function listAction()
    {
        if ($this->session->offsetExists('restaurantID')) {   
                 
            $rid = $this->session->restaurantID;
            
            return new ViewModel(array(
                'tables' => $this->getTableTable()->getTables($rid),
            ));        
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        } 
    }
    
    public function deleteAction()
    {
        if ($this->session->offsetExists('restaurantID')) {
            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                return $this->redirect()->toRoute('resttable');
            }
        
            $this->getTableTable()->deleteTable($id);
            return $this->redirect()->toRoute('resttable');
        }
        else
        {
            return $this->redirect()->toRoute('restauranthome');
        }        
    }    
    
    public function getTableTable()
    {
        if (!$this->tableTable) {
            $sm = $this->getServiceLocator();
            $this->tableTable = $sm->get('Table\Model\TableTable');
        }
        return $this->tableTable;
    }
}
