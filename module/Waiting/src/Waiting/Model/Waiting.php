<?php
namespace Waiting\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Waiting
{
    /*
    DROP TABLE waiting;
    CREATE TABLE waiting (
         id int(11) NOT NULL auto_increment,
         rid int(11),
         cid int(11),
         createdate DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
         name varchar(20) NOT NULL,
         status int NOT NULL default 0,
         note text NOT NULL,
    	PRIMARY KEY (id)
    )COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=1;
     */
    public $id;
    public $rid;
    public $cid;
    public $createdate;
    public $status;
    public $name;
    public $note;
    
    protected $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        $this->cid = (!empty($data['cid'])) ? $data['cid'] : null;
        $this->createdate = (!empty($data['createdate'])) ? $data['createdate'] : null;
                
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->note = (!empty($data['note'])) ? $data['note'] : null;
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
                'name'     => 'note',
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