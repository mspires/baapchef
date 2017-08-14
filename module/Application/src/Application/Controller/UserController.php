<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * UserController
 *
 * @author
 *
 * @version
 *
 */
class UserController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        // TODO Auto-generated indexAction() default action
        return new ViewModel();
    }
    
    public function loginAction() {
        // Store username in session
        $user_session = new Container('user');
        $user_session->username = 'Andy0708';
    
        return $this->redirect()->toRoute('welcome');
    }
    
    public function welcomeAction() {
        
        // Retrieve username from session
        $user_session = new Container('user');
        $username = $user_session->username; // $username now contains 'Andy0708'
    }    
}