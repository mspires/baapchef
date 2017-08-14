<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Reward for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Reward\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Reward\Model\Reward;
use Reward\Form\RewardForm;

class RewardController extends AbstractActionController
{
    protected $rewardTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'rewards' => $this->getRewardTable()->fetchAll(),
        )); 
    }

    public function itemAction()
    {
        return new ViewModel(array(
            'rewards' => $this->getRewardTable()->fetchAll(),
        )); 
    }
    
    public function addAction()
    {
        $form = new RewardForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $reward = new Reward();
            $form->setInputFilter($reward->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $reward->exchangeArray($form->getData());
                $this->getRewardTable()->saveReward($reward);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('reward');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('reward', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $reward = $this->getRewardTable()->getReward($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('reward', array(
                'action' => 'index'
            ));
        }
    
        $form  = new RewardForm();
        $form->bind($reward);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($reward->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getRewardTable()->saveReward($reward);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('reward');
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
            return $this->redirect()->toRoute('reward');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getRewardTable()->deleteReward($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('reward');
        }
    
        return array(
            'id'    => $id,
            'reward' => $this->getRewardTable()->getReward($id)
        );
    }
     
    public function getRewardTable()
    {
        if (!$this->rewardTable) {
            $sm = $this->getServiceLocator();
            $this->rewardTable = $sm->get('Reward\Model\RewardTable');
        }
        return $this->rewardTable;
    }    
}
