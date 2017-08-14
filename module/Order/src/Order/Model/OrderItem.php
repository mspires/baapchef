<?php
namespace Order\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class OrderItem implements InputFilterAwareInterface
{
    /*
DROP table orderitem;
CREATE TABLE orderitem (
     id int(11) NOT NULL auto_increment,
     orderid int(11) NOT NULL,
     dishid int(11) NOT NULL,
     qty int(11) NOT NULL DEFAULT 1,
     note varchar(100), 
     createdate DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
     state int(1) NOT NULL DEFAULT 0,
     PRIMARY KEY (id)
);
     */

    public $id;
    public $orderid;
    public $dishid;
    public $qty;
    public $price;
    public $note;
    public $createdate;
    public $state;
    
    public $name;
    public $image;
    
    public $rid;
    public $restaurantName;
    
    protected $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->orderid = (!empty($data['orderid'])) ? $data['orderid'] : null;
        $this->dishid = (!empty($data['dishid'])) ? $data['dishid'] : null;
        $this->createdate = (!empty($data['createdate'])) ? $data['createdate'] : null;
        $this->qty = (!empty($data['qty'])) ? $data['qty'] : null;
        $this->price = (!empty($data['price'])) ? $data['price'] : null;
        $this->note = (!empty($data['note'])) ? $data['note'] : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
        
        $this->name = (!empty($data['dishname'])) ? $data['dishname'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        $this->restaurantName  = (!empty($data['restaurantName'])) ? $data['restaurantName'] : null;
        
        if(!empty($data['restaurantName']))
        {
            $this->image = sprintf("/data/%09d/%s", $this->rid,$data['filename']);
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
                'name'     => 'orderid',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
    
            $inputFilter->add(array(
                'name'     => 'dishid',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'qty',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
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
                            'min'      => 0,
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

?>