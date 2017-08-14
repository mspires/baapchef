<?php
namespace Dish\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


class TakeoutDish implements InputFilterAwareInterface
{
    /*
     id int(11) NOT NULL auto_increment,
     rid int(11)NOT NULL,
     name varchar(100) NOT NULL,
     price DECIMAL(4,2) NOT NULL,
     note varchar(500) NOT NULL,
     */
    public $id;
    public $rid;
    public $name;
    public $price;
    public $note;
    public $filename;
    public $imagepath;
    
    public $restaurantName;
    
    protected $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->price  = (!empty($data['price'])) ? $data['price'] : null;
        $this->tax  = (!empty($data['tax'])) ? $data['tax'] : null;
        $this->note  = (!empty($data['note'])) ? $data['note'] : null;
        $this->filename  = (!empty($data['filename'])) ? $data['filename'] : null;
        
        $this->restaurantName  = (!empty($data['restaurantName'])) ? $data['restaurantName'] : null;
    
        if(!empty($data['restaurantName']))
        {
            $this->imagepath = sprintf("/data/%09d/%s", $this->rid,$data['filename']);
        }
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

?>