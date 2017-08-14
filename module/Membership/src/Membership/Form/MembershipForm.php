<?php
namespace Membership\Form;

use Zend\Form\Form;

class MembershipForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('membership');

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
            'name' => 'level',
            'type' => 'Text',
            'options' => array(
                'label' => 'Level',
            ),
        ));
        $this->add(array(
            'name' => 'status',
            'type' => 'Text',
            'options' => array(
                'label' => 'Status',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => "btn btn-primary"
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