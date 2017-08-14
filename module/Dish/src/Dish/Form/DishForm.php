<?php
namespace Dish\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;


class DishForm extends Form
{
    protected $adapter;
    protected $rid;
    
    public function getOptionsForGroup()
    {
        $dbAdapter = $this->adapter;
        $sql       = 'SELECT id, name FROM dishgroup where rid=' . $this->rid;
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();
        
        $selectData = array();
        
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['name'];
        }
        return $selectData;
    }
    
    public function __construct($dbAdapter, $rid, $name = null)
    {
        $this->adapter =$dbAdapter;
        $this->rid =$rid;
        // we want to ignore the name passed
        parent::__construct('dish');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'rid',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'groupid',
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
            'name' => 'groupid',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'groupid',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Group',
                'value_options' => $this->getOptionsForGroup(),
            )
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
