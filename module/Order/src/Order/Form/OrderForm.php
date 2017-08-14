<?php
namespace Order\Form;

use Zend\Form\Form;
use Zend\Form\View\Helper;

class OrderForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'rid',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rid',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Restaurant',
            ),            
        ));
        $this->add(array(
            'name' => 'cid',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cid',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Customer',
            ),            
        ));
        $this->add(array(
            'name' => 'dishid',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'dishid',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Dish',
            ),            
        ));           
        $this->add(array(
            'name' => 'scheduledate',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'scheduledate',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Scheduled Date',
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