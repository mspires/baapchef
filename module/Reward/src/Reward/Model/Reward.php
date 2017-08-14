<?php

namespace Reward\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Reward

{
    /*
    id int(11) NOT NULL auto_increment,
    name varchar(100) NOT NULL,
    price DECIMAL(4,2) NOT NULL,
    note varchar(500) NOT NULL,
    */
    public $id;
    public $name;
    public $price;
    public $rewardpt;
    public $note;
    public $filename;
    public $imagepath;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->price  = (!empty($data['price'])) ? $data['price'] : null;
        $this->rewardpt  = (!empty($data['rewardpt'])) ? $data['rewardpt'] : null;
        $this->note  = (!empty($data['note'])) ? $data['note'] : null;
        $this->filename  = (!empty($data['filename'])) ? $data['filename'] : null;
        
        $this->imagepath = sprintf("/data/reward/%s", $data['filename']);
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
                'name'     => 'rid',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'groupid',
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
                'name'     => 'price',
                'required' => true,
                'filters'  => array(
                    //array('name' => 'Int'),
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
