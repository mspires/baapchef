<?php
namespace Table\Model;

class Table
{
    /*
    CREATE TABLE dtable (
         id int(11) NOT NULL auto_increment,
         rid int(11)NOT NULL,
         num int(2) NOT NULL,
         name varchar(20) NOT NULL,
         status int NOT NULL default 0,
         note text NOT NULL,
    	PRIMARY KEY (id)
    )COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=1;
     */
    public $id;
    public $rid;
    public $status;
    public $num;
    public $name;
    public $note;
         
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;
        
        $this->num = (!empty($data['num'])) ? $data['num'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->note = (!empty($data['note'])) ? $data['note'] : null;
    }
    
    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}

?>