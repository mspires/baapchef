<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;

class SignupForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => 'John Doe'
            ),
            'options' => array(
                'label' => 'Name',
            )
        ));
                
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => 'example@example.com'
            ),
            'options' => array(
                'label' => 'Email',
            )
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'number',
            'attributes' => array(
                'id' => 'phone',
                'class' => 'form-control',
                'placeholder' => '4085551212'
            ),
            'options' => array(
                'label' => 'Phone',
            )
        ));
                
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
                'placeholder' => '**********'
            ),
            'options' => array(
                'label' => 'Password',                
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'loginCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Submit',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary'
            ),
        ));
        
        $this->add(array(
            'name' => 'cancel',
            'type' => 'Button',
            'attributes' => array(
                'id' => 'cancelbutton',
                'class' => 'btn btn-default'
            ),
            'options' => array(
                'label' => 'Cancel',
            ),
        ));
    }
}