<?php
namespace Message\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Captcha\Image;
use Zend\Captcha\AdapterInterface;

class RespForm extends Form
{

    public function __construct()
    {
        parent::__construct('message_form');
        
        $this->add(array(
            'name' => 'reqid',
            'type' => 'Hidden',
        ));
        
        $this->add(array(
            'name' => 'note',
            'options' => array(
                'label' => 'Response',
            ),
            'attributes' => array(
                'rows' => '10',
                'cols' => '80',
                'class' => 'form-control',
            ),
            'type'  => 'Zend\Form\Element\Textarea',
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Submit',
                'class' => 'btn btn-primary',
            ),
            'type'  => 'Zend\Form\Element\Submit',
        ));  

        $this->add(array(
            'name' => 'cancel',
            'attributes' => array(
                'id' => 'cancelbutton',
                'class' => 'btn btn-default',
            ),
            'options' => array(
                'label' => 'Cancel',
            ),
            'type'  => 'Zend\Form\Element\Button',
        ));
    }
}
