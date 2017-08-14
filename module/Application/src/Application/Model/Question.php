<?php
namespace Application\Model;

class Question
{
    public $email;
    public $phone;
    public $subject;
    public $content;

    public function exchangeArray($data)
    {
        $this->email  = (!empty($data['email'])) ? $data['email'] : null;
        $this->phone  = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->subject  = (!empty($data['subject'])) ? $data['subject'] : null;
        $this->content  = (!empty($data['content'])) ? $data['content'] : null;
    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
?>