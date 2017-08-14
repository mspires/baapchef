<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Table for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Table\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Table\Form\TableForm;
use Table\Model\Table;

class TableController extends AbstractActionController
{
    private  $tableTable;

    public function indexAction()
    {
        $rid=10000;
        return new ViewModel(array(
            'tables' => $this->getTableTable()->getTables($rid),
        ));        
    }

    public function addAction()
    {
        $rid = 10000;
        
        $form = new TableForm();
        $form->get('submit')->setValue('Add');
        $form->get('rid')->setValue($rid);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $table = new Table();
            $form->setInputFilter($table->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $table->exchangeArray($form->getData());
                $this->getTableTable()->saveTable($table);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('table');
            }
        }
        return array('form' => $form);        
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('table', array(
                'action' => 'add'
            ));
        }
        
        try {
            $table = $this->getTableTable()->getTable($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('table', array(
                'action' => 'index'
            ));
        }
        
        $form = new TableForm();
        $form->bind($table);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($table->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $this->getTableTable()->saveTable($table);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('table');
            }
        }
        
        return array(
            'id' => $id,
            'form' => $form,
            'table' => $this->getTableTable()->getTable($table->id),
        );
    }

    public function listAction()
    {
        return new ViewModel(array(
            'tables' => $this->getTableTable()->getTables(),
        ));
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
