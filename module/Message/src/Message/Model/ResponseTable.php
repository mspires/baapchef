<?php
namespace Message\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ResponseTable
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

    public function getResponses()
    {
        $resultSet = $this->tableGateway->select(function(Select $select){
            $select->order('createdate DESC')->limit(4);
        });
    
            return $resultSet;
    }
    
    public function getResponse($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveResponse(Response $response)
    {
        $data = array(
            'reqid'  => $response->reqid,
            'note' => $response->note,
        );

        $id = (int) $response->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getResponse($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteResponse($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>