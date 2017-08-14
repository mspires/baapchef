<?php
namespace Agent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Form\LoginForm;
use Auth\Form\Filter\LoginFilter;
use Zend\Session\Container;
/**
 * ProfileController
 *
 * @author
 *
 * @version
 *
 */
class ProfileController extends AbstractActionController
{
    protected  $agentTable;
    
    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('agentID')) {

            $id = $this->session->agentID;
            $agent = $this->getAgentTable()->getAgent($id);
    
            $view = new ViewModel(array(
                'agent' => $agent,
    
            ));
            
            return $view;
        }
        else 
        {
            return $this->redirect()->toRoute('agenthome');
        }
    }
    
    public function changeAction()
    {
        if ($this->session->offsetExists('agentID')) {
    
            $id = $this->session->agentID;
            $agent = $this->getAgentTable()->getAgent($id);
    
            $view = new ViewModel(array(
                'agent' => $agent,
    
            ));
    
            return $view;
        }
        else
        {
            return $this->redirect()->toRoute('agenthome');
        }
    }
    
    public function getAgentTable()
    {
        if (!$this->agentTable) {
            $sm = $this->getServiceLocator();
            $this->agentTable = $sm->get('Agent\Model\AgentTable');
        }
        return $this->agentTable;
    }
    
    public function getLogger()
    {
        if(!$this->logger)
        {
            $this->logger = $this->getServiceLocator()->get('Zend\Log');
        }
        return $this->logger;
    }
}