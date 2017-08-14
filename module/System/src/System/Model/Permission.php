<?php
namespace System\Model;

class Permission
{
    /*
    CREATE TABLE `permission` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `permission_name` varchar(45) NOT NULL,
      `resource_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`id`)
    )
     */
    public $id;
    public $permission_name;
    public $resource_id;
    public $resource_name;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->permission_name = (!empty($data['permission_name'])) ? $data['permission_name'] : null;
        $this->resource_id = (!empty($data['resource_id'])) ? $data['resource_id'] : null;
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
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'permission_name',
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
                'name'     => 'resource_id',
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