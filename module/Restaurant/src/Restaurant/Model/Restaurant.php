<?php

namespace Restaurant\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Restaurant implements InputFilterAwareInterface
{
    /*
CREATE TABLE restaurant (
	id int(11) NOT NULL auto_increment,
	createdate DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
	name varchar(100) NOT NULL,
	userid varchar(100) NOT NULL,
	password varchar(100) NOT NULL,
	email varchar(100) NOT NULL,
	phone varchar(100) NOT NULL,
	level int(1) NOT NULL DEFAULT 0,
	status enum('Y','N') NOT NULL DEFAULT 'Y',
	note varchar(500) NOT NULL,
	tax decimal(5,2) NOT NULL default 9.75,
	PRIMARY KEY (id)
)COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=10000;
    */

    public $id;
    public $createdate;
    
    public $name;
    public $email;
    public $phone;
    
    public  $level;
    public  $status;
    
    public  $tax;
    
    public $image;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->createdate = (!empty($data['createdate'])) ? $data['createdate'] : null;
        
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        //$this->userid = (!empty($data['userid'])) ? $data['userid'] : null;
        //$this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->phone = (!empty($data['phone'])) ? $data['phone'] : null;
        
        $this->level = (!empty($data['level'])) ? $data['level'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        
        $this->tax = (!empty($data['tax'])) ? $data['tax'] : null;
        
        $this->image = sprintf("/data/restaurant/%09d/%s.png", $this->id,'logo');
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

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
