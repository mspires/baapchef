<?php
namespace Cart\Form;

use Zend\Form\Form;
use Zend\Form\View\Helper;

class CartItemForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('cartitem');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'cartid',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'dishid',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'qty',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'qty',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Quantity',
            ),
        ));
        
        $this->add(array(
            'name' => 'note',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'note',
                'class' => 'form-control',
                'placeholder' => 'Enter note'
            ),
            'options' => array(
                'label' => 'Note',
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