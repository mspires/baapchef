<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Reward\Model\Reward;
use Reward\Form\RewardForm;

use Zend\Session\Container;
use Zend\Log\Logger;

use Zend\File\Transfer\Adapter;
use Zend\View\Model\JsonModel;

class RewardController extends AbstractActionController
{
    protected $rewardTable;

    /**
     * The default action - show the home page
     */
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
                return $this->redirect()->toRoute('adminreward');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminreward', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $reward = $this->getRewardTable()->getReward($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('adminreward', array(
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
                return $this->redirect()->toRoute('adminreward');
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
            return $this->redirect()->toRoute('adminreward');
        }
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getRewardTable()->deleteReward($id);
            }
    
            // Redirect to list of albums
            return $this->redirect()->toRoute('adminreward');
        }
    
        return array(
            'id'    => $id,
            'reward' => $this->getRewardTable()->getReward($id)
        );
    }
     
    public function uploaddragfileAction()
    {
        $valid = false;
        $name = "";
        $url = "";
        $message = "";
    
        if ($this->getRequest()->isPost())
        {
            $postdata = $this->getRequest()->getPost()->toArray();
    
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $destination = './data/data/reward';
            $adapter->setDestination($destination);
    
            $files  = $adapter->getFileInfo();
            foreach($files as $file => $fileInfo) {
    
                if($postdata['rewardfilename'] == null)
                {
                    $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                    $newFilename = date('YmdHi') . '-' . uniqid() . '.' . $ext;
                }
                else
                {
                    $newFilename = $postdata['rewardfilename'];
                }
    
                $adapter->addFilter('Rename', array(
                    'target' => $newFilename,
                    'overwrite'  => true
                ));
    
                if (!$adapter->receive($fileInfo['name'])) {
    
                    $messages = $adapter->getMessages();
                }
                else {
    
                    //list($width, $height) = getimagesize("C:\xampp\htdocs\baapchef\public\data\" . $newFilename);
                    $imagePath = sprintf('/data/reward/%s', $newFilename);
    
                    $valid = true;
                    $name = $newFilename;
                    $url = sprintf("<img src='%s?r=%d' style='width:200px' />", $imagePath,rand(1,100));
                }
            }
        }
    
        $data = array(  'valid'=>$valid,
            'name'=>htmlentities($name),
            'fullname'=>htmlentities($url),
            'url'=>$url,
            'message'=>$message
        );
    
        $result = new JsonModel($data);
    
        return $result;
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