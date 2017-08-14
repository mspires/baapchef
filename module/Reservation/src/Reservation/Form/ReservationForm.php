<?php
namespace Reservation\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class ReservationForm extends Form
{
    public function getOptionsForStatus()
    {
        $selectData = array('1' => 'Active', '0' => 'Disable');
        return $selectData;
    }
    
    public function __construct()
    {
        parent::__construct('waiting_form');

        $this->add(array(
            'name' => 'rid',
            'type' => 'Hidden',
        ));
                
        
        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => 'Name',
            ),
            'attributes' => array(
                'size' => '80',
                'class' => 'form-control',
            ),
            'type'  => 'Zend\Form\Element\Text',
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
            'name' => 'note',
            'options' => array(
                'label' => 'Note',
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
