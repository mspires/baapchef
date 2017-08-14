<?php
namespace Message\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Captcha\Image;
use Zend\Captcha\AdapterInterface;

class ReqForm extends Form
{

    protected $captcha;
    
    public function __construct()
    {
        parent::__construct('message_form');

        $this->captcha = new Image(array(
            'expiration' => '300',
            'wordlen' => '4',
            'font' => 'public/data/fonts/arial.ttf',
            'fontSize' => '25',
            'imgDir' => 'public/captcha',
            'imgUrl' => '/captcha'
        ));
        
        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => 'Name',
            ),
            'attributes' => array(
                'size' => '50'
            ),
            'type'  => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'email',
            'options' => array(
                'label' => 'Email address',
            ),
            'attributes' => array(
                'size' => '50',
                'class' => 'form-control',
            ),
            'type'  => 'Zend\Form\Element\Email',
        ));
        
        $this->add(array(
            'name' => 'subject',
            'options' => array(
                'label' => 'Subject',
            ),
            'attributes' => array(
                'size' => '50',
                'class' => 'form-control',
            ),
            'type'  => 'Zend\Form\Element\Text',
        ));
        
        $this->add(array(
            'name' => 'note',
            'options' => array(
                'label' => 'Message',
            ),
            'attributes' => array(
                'rows' => '10',
                'cols' => '50',
                'class' => 'form-control',
            ),
            'type'  => 'Zend\Form\Element\Textarea',
        ));

        $this->add(array(
            'name' => 'captcha',
            'options' => array(
                'label' => 'Verification',
                'captcha' => $this->captcha,
                'class' => 'form-control',
            ),
            'type'  => 'Zend\Form\Element\Captcha',
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'reqCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
            )
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