<?php
namespace Restaurant\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Address implements InputFilterAwareInterface
{
    /*
CREATE TABLE address (
	id int(11) NOT NULL auto_increment,
	type ENUM('customer', 'restaurant', 'agent'),
	address1 varchar(100) NOT NULL,
	address2 varchar(100),
	city varchar(100) NOT NULL,
	state varchar(100) NOT NULL,
	zip varchar(10) NOT NULL,
	country varchar(3) NOT NULL,
	PRIMARY KEY (id)
);
    */
    public $id;
    public $type;
    public $tid;
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $lat;
    public $lng;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->tid = (!empty($data['tid'])) ? $data['tid'] : null;
        $this->address1 = (!empty($data['address1'])) ? $data['address1'] : null;
        $this->address2 = (!empty($data['address2'])) ? $data['address2'] : null;
        $this->city = (!empty($data['city'])) ? $data['city'] : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
        $this->zip = (!empty($data['zip'])) ? $data['zip'] : null;
        $this->country = (!empty($data['country'])) ? $data['country'] : null;
        $this->lat = (!empty($data['lat'])) ? $data['lat'] : null;
        $this->lng = (!empty($data['lng'])) ? $data['lng'] : null;
    }
    
    public function toString()
    {
        if($this->address1) {
            $address = $this->address1;
        }
        if($this->address2) {
            $address = ' ' + $this->address2;
        }
        if($this->city) {
            $address = ' ' + $this->city;
        }
        if($this->state) {
            $address = ' ' + $this->state;
        }
        
        if($this->zip) {
            $address = ' ' + $this->zip;
        }

        /*
        if($this->country) {
            $address = ' ' + $this->country;
        }
        */
        return $address;
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
                'name'     => 'type',
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
                'name'     => 'tid',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
                        
            $inputFilter->add(array(
                'name'     => 'address1',
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
                'name'     => 'address2',
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
                            'max'      => 100,
                        ),
                    ),
                ),
            ));
                        
            $inputFilter->add(array(
                'name'     => 'city',
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
                'name'     => 'state',
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
                'name'     => 'zip',
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
                'name'     => 'country',
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
                'name'     => 'lat',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'lng',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
                        
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}