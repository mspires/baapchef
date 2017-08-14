<?php

namespace User\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User implements InputFilterAwareInterface

{
    /*
    DROP TABLE users;
    CREATE TABLE `users` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `rid` int(10) NOT NULL,
      `usertype` enum('Admin','Agent','Restaurant') NOT NULL DEFAULT 'Restaurant',
      `name` varchar(100) NOT NULL,
      `userid` varchar(100) NOT NULL,
      `email` varchar(100) NOT NULL,
      `phone` varchar(20) NOT NULL,
      `password` varchar(20) NOT NULL,
      `status` enum('Y','N') NOT NULL DEFAULT 'Y',
      `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    );
    */
    
    const USER_ADMIN     = 'Admin';
    const USER_AGENT     = 'Agent';
    const  USER_RESTAURANT = 'Restaurant';
    
    const DEFAULT_USER_ADMIN_ID   = 1;
    
    public $id;
    public $usertype;
    public $rid;
    public $name;
    public $userid;
    public $email;
    public $phone;
    public $status;
    
    public $role_id;
    public $role_name;
    
    public $image;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->usertype = (!empty($data['usertype'])) ? $data['usertype'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->userid  = (!empty($data['userid'])) ? $data['userid'] : null;
        $this->email  = (!empty($data['email'])) ? $data['email'] : null;
        $this->phone  = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->password  = (!empty($data['password'])) ? $data['password'] : null;
        $this->status  = (!empty($data['status'])) ? $data['status'] : null;
        
        $this->role_id  = (!empty($data['role_id'])) ? $data['role_id'] : null;
        $this->role_name  = (!empty($data['role_name'])) ? $data['role_name'] : null;
        
        $this->image = "/img/person.png";
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
                'name'     => 'usertype',
                'required' => true,
            ));
                        
            $inputFilter->add(array(
                'name'     => 'rid',
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
                'name'     => 'password',
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
                            'min'      => 4,
                            'max'      => 4,
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
                            'max'      => 20,
                        ),
                    ),
                ),
            ));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
