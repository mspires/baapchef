<?php
namespace Alert\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class AlertTable
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

    public function getAlerts()
    {
        $resultSet = $this->tableGateway->select(function(Select $select){
            $select->order('createdate DESC')->limit(4);
        });
    
            return $resultSet;
    }
    
    public function getAlert($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveAlert(Alert $alert)
    {
        $data = array(
            'subject' => $alert->subject,
            'note' => $alert->note,
        );

        $id = (int) $alert->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAlert($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteAlert($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>