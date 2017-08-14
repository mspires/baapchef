<?php
namespace Cart\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Cart implements InputFilterAwareInterface
{
    /*
    CREATE TABLE cart (
    	id int(11) NOT NULL auto_increment,
    	rid int(11) NOT NULL,
    	cid int(11) NOT NULL,
    	createdate DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    	state int(1) NOT NULL DEFAULT 0,
    	PRIMARY KEY (id)
    );
     */
    public $id;
    public $rid;
    public $cid;
    public $createdate;
    public $state;

    public $rimage;
    public $cimage;

    public  $cartitems;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        $this->cid = (!empty($data['cid'])) ? $data['cid'] : null;
        $this->createdate = (!empty($data['createdate'])) ? $data['createdate'] : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
        
        $this->rimage = sprintf("/data/restaurant/%09d/%s.png", $this->rid,'logo');
        $this->cimage = sprintf("/data/restaurant/%09d/%s.png", $this->rid,'customer');
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
                'name'     => 'cid',
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