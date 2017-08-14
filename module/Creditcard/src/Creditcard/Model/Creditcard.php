<?php

namespace Creditcard\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Creditcard implements InputFilterAwareInterface
{
    /*
    CREATE TABLE creditcard (
    	id int(11) NOT NULL auto_increment,
    	type ENUM('customer', 'restaurant'),
    	tid int(11) NOT NULL,
    	number varchar(50) NOT NULL,
    	name varchar(100) NOT NULL,
    	cvs varchar(10) NOT NULL,
    	year varchar(4) NOT NULL,
    	month varchar(4) NOT NULL,
    	PRIMARY KEY (id)
    )
    */
    public $id;
    public $type;
    public $tid;
    public $number;
    public $name;
    public $cvs;
    public $year;
    public $month;
    
    public  $image;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->tid = (!empty($data['tid'])) ? $data['tid'] : null;
        $this->number = (!empty($data['number'])) ? $data['number'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->cvs = (!empty($data['cvs'])) ? $data['cvs'] : null;
        $this->year = (!empty($data['year'])) ? $data['year'] : null;
        $this->month = (!empty($data['month'])) ? $data['month'] : null;
        
        $this->image = "/img/card.png";
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
                'name'     => 'tid',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'number',
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
                'name'     => 'year',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'month',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'cvs',
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
                            'min'      => 3,
                            'max'      => 4,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}