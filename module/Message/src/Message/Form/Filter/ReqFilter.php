<?php
namespace Message\Form\Filter;

use Zend\InputFilter\InputFilter;

class ReqFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'subject',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
        ));
        
        $this->add(array(
            'name' => 'note',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
        ));
        
    }
}