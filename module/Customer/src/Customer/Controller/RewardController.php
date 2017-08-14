<?php
namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Reward\Model\Reward;

use Zend\Session\Container;
use Zend\Log\Logger;

use Zend\View\Model\JsonModel;
use Zend\Json\Json;

class RewardController extends AbstractActionController
{
    protected $rewardTable;
    
    protected $session;
    protected $logger;
    
    private $acceptCriteria = array(
        'Zend\View\Model\ViewModel' => array(
            'text/html',
        ),
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        ));
    
    public function indexAction()
    {
        // TODO Auto-generated RewardController::indexAction() default action
        return new ViewModel();
    }
    
    public function itemAction()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeaders($headers);
        $request = $this->getRequest();
        
        $view = $this->acceptableViewModelSelector($this->acceptCriteria);
        Json::$useBuiltinEncoderDecoder = true;
                
        if ($request->isPost()) {
        
            $data = $request->getPost();

            $view->setVariables(array(
                'rewards' => $this->getRewardTable()->fetchAll(),
            ));
        }
        else
        {
            $view->setVariables(array(
                'rewards' => $this->getRewardTable()->fetchAll(),
            ));
        }
        
        return $view;        
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