<?php
namespace Creditcard\Form;

use Zend\Form\Form;

class CreditcardForm extends Form
{
    public function getOptionsForMonth()
    {
            return array(   '01' => '01',
                            '02' => '02',
                            '03' => '03',
                            '04' => '04',
                            '05' => '05',
                            '06' => '06',
                            '07' => '07',
                            '08' => '08',
                            '09' => '09',
                            '10' => '00',
                            '11' => '11',
                            '12' => '12',
            );
    }

    public function getOptionsForYear()
    {
        $yearArry = array();
        
        for($i = 0; $i < 5 ; $i++)
        {
            $year = date('Y') + $i;
            $yearArry[$year] = $year;
        }
        
        return $yearArry;
    }
        
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('creditcard');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'type',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'tid',
            'type' => 'Hidden',
        ));                
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => 'Enter name on card'
            ),            
            'options' => array(
                'label' => 'Name on Card: ',
            ),
        ));        
        $this->add(array(
            'name' => 'number',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'number',
                'class' => 'form-control',
                'placeholder' => 'Enter card number'
            ),            
            'options' => array(
                'label' => 'Card Number: ',
            ),
        ));   

        $this->add(array(
            'name' => 'month',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'month',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Expired Month: ',
                'value_options' => $this->getOptionsForMonth(),
            )
        ));

        $this->add(array(
            'name' => 'year',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'month',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Expired Year: ',
                'value_options' => $this->getOptionsForYear(),
            )
        ));
        
        $this->add(array(
            'name' => 'cvs',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cvs',
                'class' => 'form-control',
                'placeholder' => 'Enter security code'
            ),
            'options' => array(
                'label' => 'Security Code: ',
                
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

?>