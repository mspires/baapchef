<?php
namespace Order\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ROrderType {

    const RORDER_INSHOP = "In Restaurant";
    const RORDER_TAKEAWAY = "Take away";
    const RORDER_TOGO = "To Go";
    const RORDER_DELIVERY = "Delivery";
    
    private static $enum = array(1 => ROrderType::RORDER_INSHOP, 
                                 2 => ROrderType::RORDER_TAKEAWAY, 
                                 3 => ROrderType::RORDER_TOGO, 
                                 4 => ROrderType::RORDER_DELIVERY);

    public static  function toOrdinal($name) {
        return array_search($name, self::$enum);
    }

    public static  function toString($ordinal) {
        return self::$enum[$ordinal];
    }
}

class ROrderState {
    
    const  RORDERSTATE_CANCEL = "Cancel";
    const  RORDERSTATE_INORDER = "In Order";
    const RORDERSTATE_COOKING = "Cooking";
    const RORDERSTATE_OUT = "Out";
    
    private static $enum = array(-1 => ROrderState::RORDERSTATE_CANCEL, 
                                 1 => ROrderState::RORDERSTATE_INORDER, 
                                 2 => ROrderState::RORDERSTATE_COOKING, 
                                 3 => ROrderState::RORDERSTATE_OUT);

    public static  function toOrdinal($name) {
        return array_search($name, self::$enum);
    }

    public static  function toString($ordinal) {
        return self::$enum[$ordinal];
    }
}

class COrderState {

    const RORDERSTATE_INORDER = "In Order";
    const RORDERSTATE_COOKING = "Cooking";
    const RORDERSTATE_OUT = "Out";

    private static $enum = array(1 => ROrderState::RORDERSTATE_INORDER,
        2 => ROrderState::RORDERSTATE_COOKING,
        3 => ROrderState::RORDERSTATE_OUT);

    public static  function toOrdinal($name) {
        return array_search($name, self::$enum);
    }

    public static  function toString($ordinal) {
        return self::$enum[$ordinal];
    }
}

class Order implements InputFilterAwareInterface
{
    
    /*
    CREATE TABLE orderbox (
    	id int(11) NOT NULL auto_increment,
    	rid int(11) NOT NULL,
    	cid int(11) NOT NULL,
    	ordertype int(1) DEFAULT 1,
    	orderdate DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    	scheduledate DATETIME DEFAULT NULL,
    	takeoutdate DATETIME DEFAULT NULL,
    	rstate int(1) DEFAULT 1,
    	cstate int(1) DEFAULT 1,
    	PRIMARY KEY (id)
    );
    */
    
    public $id;
    public $rid;
    public $cid;
    
    public $ordertype;
    public $orderdate;
    public $scheduledate;
    public $takeoutdate;
    
    public $rstate;
    public $cstate;
    
    public $rname;
    public $cname;
    
    public $rimage;
    public $cimage;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        $this->cid = (!empty($data['cid'])) ? $data['cid'] : null;
        $this->ordertype = (!empty($data['ordertype'])) ? ROrderType::toString($data['ordertype']) : null;
        $this->orderdate = (!empty($data['orderdate'])) ? $data['orderdate'] : null;
        $this->scheduledate = (!empty($data['scheduledate'])) ? $data['scheduledate'] : null;
        $this->takeoutdate = (!empty($data['takeoutdate'])) ? $data['takeoutdate'] : null;

        $this->rstate = (!empty($data['rstate'])) ? ROrderState::toString($data['rstate']) : null;
        $this->cstate = (!empty($data['cstate'])) ? COrderState::toString($data['cstate']) : null;
        
        $this->rname = (!empty($data['rname'])) ? $data['rname'] : null;
        $this->cname = (!empty($data['cname'])) ? $data['cname'] : null;
        
        $this->rimage = sprintf("/data/restaurant/%09d/%s.png", $this->rid,'logo');
        $this->cimage = sprintf("/data/customer/%09d/%s.png", $this->rid,'customer');
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

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}