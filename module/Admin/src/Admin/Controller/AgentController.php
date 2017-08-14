<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Agent for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Agent\Model\Agent;
use Agent\Form\AgentForm;
use Zend\Session\Container;
use Zend\Log\Logger;

use Auth\Utility\UserPassword;

use Admin\Model\SendEmail;

class AgentController extends AbstractActionController
{
    protected $agentTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('adminID')) {
        
            return new ViewModel(array(
                'agents' => $this->getAgentTable()->fetchAll(),
            ));
        } 
        else
        {
        
            return $this->redirect()->toRoute('adminhome');
        }  
    }
    
    public function addAction()
    {
        $form = new AgentForm();
        $form->get('submit')->setValue('Add');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $agent = new Agent();
            $form->setInputFilter($agent->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $agent->exchangeArray($form->getData());
                $this->getAgentTable()->saveAgent($agent);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('agent');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminagent', array(
                'action' => 'add'
            ));
        }

        $this->session->agentID = $id;
        $agent = $this->getAgentTable()->getAgent($id);
    
        $form  = new AgentForm();
        $form->bind($agent);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($agent->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getAgentTable()->saveAgent($agent);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('adminagent');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
            'agent' => $agent,
        );
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
            $destination = sprintf('./data/data/agent/%09d',$id);
            $adapter->setDestination($destination);
    
            $files  = $adapter->getFileInfo();
            foreach($files as $file => $fileInfo) {
    
                if($postdata['dishfilename'] == null)
                {
                    $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                    $newFilename = 'agent' . '.' . $ext;
                }
                else
                {
                    $newFilename = $postdata['dishfilename'];
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
    
    public function ResetpwdAction()
    {
        $valid = false;
        $message = "";
        
        if ($this->session->offsetExists('adminID')) {
        
            if ($this->session->offsetExists('agentID')) {
                
                $id = $this->session->agentID;
                
                $userPassword = new UserPassword();
                $password = $userPassword->generate(8);
                $encyptPass = $userPassword->create($password);

                $agent = $this->getAgentTable()->getAgent($id);
                $agent->password = $encyptPass;
                
                $this->getAgentTable()->saveAgent($agent);
                        
                $email = new SendEmail($this);
                $email->resetpwd($agent, $password);
                
                $valid = true;
            }
        }
        
        $data = array(  'valid'=>$valid,
                        'message'=>$message
        );
        
        $result = new JsonModel($data);
        
        return $result;
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('adminagent');
        }
    
        $this->getAgentTable()->deleteAgent($id);
        return $this->redirect()->toRoute('adminagent');
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
