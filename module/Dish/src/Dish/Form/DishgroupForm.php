<?php
namespace Dish\Form;

use Zend\Form\Form;

class DishgroupForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('dishgroup');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'rid',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
        $this->add(array(
            'name' => 'note',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'note',
                'class' => 'form-control',
                'placeholder' => ''
            ),
            'options' => array(
                'label' => 'Description',
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