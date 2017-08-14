<?php
namespace Message\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Response
{
    /*
    DROP TABLE responsemsg;
    CREATE TABLE responsemsg (
         id int(11) NOT NULL auto_increment,
         reqid int(11) NOT NULL DEFAULT 0,
         createdate DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
         email varchar(100) NOT NULL,
         level int(1) NOT NULL DEFAULT 0,
    	 status enum('Y','N') NOT NULL DEFAULT 'Y',
         note text NOT NULL,
    	PRIMARY KEY (id)
    )COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=1;
     */
    public $id;
    public $reqid;
    public $createdate;
    public $email;
    public $level;
    public $status;
    public $note;
    
    protected $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->reqid = (!empty($data['reqid'])) ? $data['reqid'] : null;
        $this->createdate = (!empty($data['createdate'])) ? $data['createdate'] : null;
        
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        
        $this->level = (!empty($data['level'])) ? $data['level'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        
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
                'name'     => 'reqid',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
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