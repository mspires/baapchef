<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class UserForm extends Form
{
    protected $adapter;
    
    public function getOptionsForRole()
    {
        $dbAdapter = $this->adapter;
        $sql       = 'SELECT rid, role_name FROM role';
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();
    
        $selectData = array();
    
        foreach ($result as $res) {
            $selectData[$res['rid']] = $res['role_name'];
        }
        return $selectData;
    }

    public function getOptionsForStatus()
    {
        $selectData = array('Y' => 'Active', 'N' => 'Disable');
        return $selectData;
    }
    
    public function __construct(AdapterInterface $dbAdapter, $name = null)
    {
        $this->adapter =$dbAdapter;
        // we want to ignore the name passed
        parent::__construct('users');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'usertype',
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
                'placeholder' => 'John Doe'
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'id' => 'userid',
                'class' => 'form-control',
                'placeholder' => 'example@email.com'
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
                'label' => 'Phone Number',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
                'placeholder' => '****'
            ),
            'options' => array(
                'label' => 'PIN',
            ),
        ));

        $this->add(array(
            'name' => 'role_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'role_id',
                'class' => 'form-control',
                'placeholder' => 'staff'
            ),
            'options' => array(
                'label' => 'Role',
                'empty_option' => '',
                'value_options' => $this->getOptionsForRole(),
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
                'empty_option' => '',
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
            'type' => 'Zend\Form\Element\Button',
            'attributes' => array(
                'id' => 'cancelbutton',
                'class' => 'btn btn-default'
            ),
            'options'=>array(
                'label'=>'Cancel'
            )
        ));
    }
}
