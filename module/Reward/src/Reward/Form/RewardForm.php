<?php
namespace Reward\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;


class RewardForm extends Form
{
    protected $adapter;
    protected $rid;
    
    public function __construct($name = null)
    {
        parent::__construct('reward');

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
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
        
        $this->add(array(
            'name' => 'price',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'price',
                'class' => 'form-control',
                'placeholder' => '0.00'
            ),
            'options' => array(
                'label' => 'Price',
            ),
        ));  
              
        $this->add(array(
            'name' => 'note',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'note',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Description',
            ),
        ));   

        $this->add(array(
            'name' => 'filename',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'filename',
                'class' => 'form-control',
                'readonly' => 'true',
            ),
            'options' => array(
                'label' => 'Image',
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
