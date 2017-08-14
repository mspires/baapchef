<?php
namespace Agent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Form\LoginForm;
use Auth\Form\Filter\LoginFilter;
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

    /**
     * The default action - show the home page
     */

    public function indexAction()
    {
        $request = $this->getRequest();
    
        $key = $this->params()->fromRoute('key', sha1('customer'));
    
        $view = new ViewModel();
        $signupForm = new LoginForm('signupForm');
        $signupForm->setInputFilter(new LoginFilter());
    
        $signupForm->get('role')->setValue($key);
    
        if ($request->isPost()) {
            $data = $request->getPost();
            $signupForm->setData($data);
    
            if ($signupForm->isValid()) {
                $data = $signupForm->getData();
    
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
    
                return $this->redirect()->toUrl('/login', array('key' => $key));
            } else {
                $errors = $signupForm->getMessages();
            }
        }
    
        $view->setVariable('signupForm', $signupForm);
        return $view;
    }
}