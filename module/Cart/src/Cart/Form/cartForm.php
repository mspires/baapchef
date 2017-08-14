<?php
namespace Cart\Form;

use Zend\Form\Form;
use Zend\Form\View\Helper;

class CartForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('cart');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'rid',
            'type' => 'Text',
            'options' => array(
                'label' => 'Restaurant',
            ),            
        ));
        $this->add(array(
            'name' => 'cid',
            'type' => 'Text',
            'options' => array(
                'label' => 'Customer',
            ),            
        ));          
        $this->add(array(
            'name' => 'createdate',
            'type' => 'Text',
            'options' => array(
                'label' => 'Create Date',
            ),
        ));                
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
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

?>