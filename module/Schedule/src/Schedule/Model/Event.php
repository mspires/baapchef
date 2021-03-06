<?php
namespace Event\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Event
{
    /*
    CREATE TABLE eventmsg (
         id int(11) NOT NULL auto_increment,
         createdate DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
         level int(1) NOT NULL DEFAULT 0,
    	 status enum('Y','N') NOT NULL DEFAULT 'Y',
         subject varchar(100) NOT NULL,
         note text NOT NULL,
    	PRIMARY KEY (id)
    )COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=1;
     */
    public $id;
    public $createdate;
    public $level;
    public $status;
    public $subject;
    public $note;
    
    protected $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->createdate = (!empty($data['createdate'])) ? $data['createdate'] : null;
                
        $this->level = (!empty($data['level'])) ? $data['level'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        
        $this->subject = (!empty($data['subject'])) ? $data['subject'] : null;
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
                'name'     => 'subject',
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