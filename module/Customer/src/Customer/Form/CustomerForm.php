<?php
namespace Customer\Form;

use Zend\Form\Form;

class CustomerForm extends Form
{
    public function getOptionsForLevel()
    {
        $selectData = array('1' => 'Silver',
            '2' => 'Gold',
            '3' => 'Dimond',
        );
        return $selectData;
    }
    
    public function getOptionsForStatus()
    {
        $selectData = array('Y' => 'Active', 'N' => 'Disable');
        return $selectData;
    }
    
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('customer');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => 'John Doe'
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));        
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => 'sample@email.com'
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'phone',
                'class' => 'form-control',
                'placeholder' => '4085551212'
            ),
            'options' => array(
                'label' => 'Phone',
            ),
        ));    

        $this->add(array(
            'name' => 'level',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'level',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Level',
                'value_options' => $this->getOptionsForLevel(),
            )
        ));
        
        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'status',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Status',
                'value_options' => $this->getOptionsForStatus(),
            )
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