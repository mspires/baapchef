<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Question;
use Application\Service\SendEmail;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function aboutAction()
    {
        return new ViewModel();
    }

    public function jobAction()
    {
        return new ViewModel();
    }
        
    public function termsAction()
    {
        return new ViewModel();
    }
    
    public function policyAction()
    {
        return new ViewModel();
    }

    public function contactusAction()
    {
        $message = '';
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $data = $request->getPost()->toArray();
            
            $question = new Question();
            $question->exchangeArray($data);
            
            $email = new SendEmail($this);
            $email->sendQuestion($question);
            
            $message = 'OK';
        }

        return new ViewModel(array('message' => $message ));
    }
    
    public function faqAction()
    {
        return new ViewModel();
    }

    public function publisherAction()
    {
        return new ViewModel();
    }
}
