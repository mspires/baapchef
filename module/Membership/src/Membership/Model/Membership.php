<?php
namespace Membership\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Membership implements InputFilterAwareInterface
{
    /*
    CREATE TABLE membership (
    	id int(11) NOT NULL auto_increment,
    	rid int(11) NOT NULL DEFAULT 0,
    	cid int(11) NOT NULL DEFAULT 0,
    	level int(1) NOT NULL DEFAULT 0,  
    	status int(1) NOT NULL DEFAULT 0,  
    	note varchar(500) NOT NULL,
    	PRIMARY KEY (id)
    );
     */
    
    const SILVER    = 1;
    const GOLD      = 2;
    const DIAMOND   = 3;
    
    const MEMBERSHIP_PENDING = 1;
    const MEMBERSHIP_APPROVED = 2;
    const MEMBERSHIP_CANCELLED = 3;
    
    public $id;
    public $rid;
    public $cid;
    public $level;
    public $status;
    public $note;
    
    public  $restaurantName;
    public  $restaurantPhone;
    public  $customerName;
    public  $customerPhone;
    public $rimage;
    public $cimage;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        $this->cid = (!empty($data['cid'])) ? $data['cid'] : null;
        $this->level = (!empty($data['level'])) ? $data['level'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->note = (!empty($data['note'])) ? $data['note'] : null;
        
        $this->restaurantName = (!empty($data['restaurantName'])) ? $data['restaurantName'] : null;
        $this->restaurantPhone = (!empty($data['restaurantPhone'])) ? $data['restaurantPhone'] : null;
        $this->customerName = (!empty($data['customerName'])) ? $data['customerName'] : null;
        $this->customerPhone = (!empty($data['customerPhone'])) ? $data['customerPhone'] : null;
        
        $this->rimage = sprintf("/data/restaurant/%09d/%s.png", $this->rid,'logo');
        $this->cimage = sprintf("/data/customer/%09d/%s.png", $this->cid,'customer');
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
                'name'     => 'artist',
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
                'name'     => 'title',
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

?>