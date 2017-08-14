<?php
namespace Restaurant\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class AddressForm extends Form
{
    protected $adapter;
    
    public function getOptionsForState()
    {
        $dbAdapter = $this->adapter;
        $sql       = 'SELECT statecode, statename FROM state';
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();
    
        $selectData = array();
    
        foreach ($result as $res) {
            $selectData[$res['statecode']] = $res['statename'];
        }
        return $selectData;
    }
        
    public function __construct($dbAdapter, $name = null)
    {
        $this->adapter =$dbAdapter;
        
        // we want to ignore the name passed
        parent::__construct('address');

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
            'name' => 'address1',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'address1',
                'class' => 'form-control',
                'placeholder' => ''
            ),
            'options' => array(
                'label' => 'Address1',
            ),
        ));        
        $this->add(array(
            'name' => 'address2',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'address2',
                'class' => 'form-control',
                'placeholder' => ''
            ),
            'options' => array(
                'label' => 'Address2',
            ),
        ));
        $this->add(array(
            'name' => 'city',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'city',
                'class' => 'form-control',
                'placeholder' => 'San Jose'
            ),
            'options' => array(
                'label' => 'City',
            ),
        ));    
        $this->add(array(
            'name' => 'state',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'state',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'State',
                'value_options' => $this->getOptionsForState(),
            )
        ));            
        $this->add(array(
            'name' => 'zip',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'zip',
                'class' => 'form-control',
                'placeholder' => '95134'
            ),
            'options' => array(
                'label' => 'State',
            ),
        ));        
        $this->add(array(
            'name' => 'country',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'country',
                'class' => 'form-control',
                'placeholder' => 'US'
            ),
            'options' => array(
                'label' => 'Country',
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