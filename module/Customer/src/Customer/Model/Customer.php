<?php

namespace Customer\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Customer implements InputFilterAwareInterface

{
    /*
    CREATE TABLE customer (
    	id int(11) NOT NULL auto_increment,
    	name varchar(100) NOT NULL,
    	userid varchar(100) NOT NULL,
    	password varchar(100) NOT NULL,
    	email varchar(100) NOT NULL,
    	phone varchar(100) NOT NULL,
    	note varchar(500) NOT NULL,
    	authcode varchar(4),
    	PRIMARY KEY (id)
    );
    */
    public $id;
    public $name;
    public $userid;
    public $password;
    public $email;
    public $phone;
    public $note;
    
    public $level;
    public $status;
    
    public $image;
        
    protected $inputFilter;
    protected $authcode;
    
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->email  = (!empty($data['email'])) ? $data['email'] : null;
        $this->phone  = (!empty($data['phone'])) ? $data['phone'] : null;
        
        $this->password  = (!empty($data['password'])) ? $data['password'] : null;
        
        $this->note  = (!empty($data['note'])) ? $data['note'] : null;

        $this->level  = (!empty($data['level'])) ? $data['level'] : null;
        $this->status  = (!empty($data['status'])) ? $data['status'] : null;
        
        $this->image = sprintf("/data/customer/%09d/customer.jpeg", $this->id);
        
        $this->authcode  = (!empty($data['authcode'])) ? $data['authcode'] : null;
    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));  
                        
            $inputFilter->add(array(
                'name'     => 'phone',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));          

            $inputFilter->add(array(
                'name'     => 'note',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 500,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
