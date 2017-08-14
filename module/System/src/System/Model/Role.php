<?php
namespace System\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Role implements InputFilterAwareInterface
{
    /*
    CREATE TABLE `role` (
      `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `role_name` varchar(45) NOT NULL,
      `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
      PRIMARY KEY (`rid`)
    )
     */
    public $rid;
    public $role_name;
    public $status;
    
    public $permission_name;
    public $resource_name;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        $this->role_name = (!empty($data['role_name'])) ? $data['role_name'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        
        $this->permission_name = (!empty($data['permission_name'])) ? $data['permission_name'] : null;
        $this->resource_name = (!empty($data['resource_name'])) ? $data['resource_name'] : null;
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
                'name'     => 'rid',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'role_name',
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
                'name'     => 'status',
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