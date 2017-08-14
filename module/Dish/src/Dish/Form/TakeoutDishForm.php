<?php
namespace Dish\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;


class TakeoutDishForm extends Form
{
    public function getOptionsForPrice()
    {
        $selectData = array('8.00' => '8.00',
                            '10.00' => '10.00',
                            '12.00' => '12.00',);
    
        return $selectData;
    }
    
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('takeoutdish');

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
            'name' => 'price',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'price',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Price',
                'value_options' => $this->getOptionsForPrice(),
            )
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

?>