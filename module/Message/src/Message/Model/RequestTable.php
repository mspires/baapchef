<?php
namespace Message\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class RequestTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getRequests()
    {
        $resultSet = $this->tableGateway->select(function(Select $select){
            $select->order('createdate DESC')->limit(4);
        });
        
        return $resultSet;
    }
    
    public function getRequest($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveRequest(Request $request)
    {
        $data = array(
            'subject' => $request->subject,
            'note' => $request->note,
        );

        $id = (int) $request->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRequest($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteRequest($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>