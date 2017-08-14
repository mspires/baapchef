<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Agent for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Agent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Agent\Model\Agent;
use Agent\Form\AgentForm;
use Zend\Session\Container;

class AgentController extends AbstractActionController
{
    protected $agentTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    
    }
      
    public function editAction()
    {
        if ($this->session->offsetExists('agentID')) {
        
            $id = $this->session->agentID;
                    
        
            try {
                $agent = $this->getAgentTable()->getAgent($id);
            }
            catch (\Exception $ex) {
                return $this->redirect()->toRoute('agent', array(
                    'action' => 'index'
                ));
            }
    
            $form  = new AgentForm();
            $form->bind($agent);
            $form->get('submit')->setAttribute('value', 'Edit');
        
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setInputFilter($agent->getInputFilter());
                $form->setData($request->getPost());
        
                if ($form->isValid()) {
                    $this->getAgentTable()->saveAgent($agent);
        
                    return $this->redirect()->toRoute('agent');
                }
            }
        
            return array(
                'id' => $id,
                'form' => $form,
                'agent' => $agent,
            );
        }
        else
        {
            return $this->redirect()->toRoute('agenthome');
        }
    }

    public function uploaddragfileAction()
    {
        $valid = false;
        $name = "";
        $url = "";
        $message = "";
    
        $id = $this->session->agentID;
    
        if ($this->getRequest()->isPost())
        {
            $postdata = $this->getRequest()->getPost()->toArray();
    
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $destination = sprintf('./public/data/agent/%09d',$id);
            $adapter->setDestination($destination);
    
            $files  = $adapter->getFileInfo();
            foreach($files as $file => $fileInfo) {
    
                $newFilename = 'agent.png';
    
                $adapter->addFilter('Rename', array(
                    'target' => $newFilename,
                    'overwrite'  => true
                ));
    
                if (!$adapter->receive($fileInfo['name'])) {
    
                    $messages = $adapter->getMessages();
                }
                else {
    
                    //list($width, $height) = getimagesize("C:\xampp\htdocs\baapchef\public\data\" . $newFilename);
                    $imagePath = sprintf('/data/agent/%09d/%s', $id, $newFilename);
    
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
        
    public function getAgentTable()
    {
        if (!$this->agentTable) {
            $sm = $this->getServiceLocator();
            $this->agentTable = $sm->get('Agent\Model\AgentTable');
        }
        return $this->agentTable;
    }
}
