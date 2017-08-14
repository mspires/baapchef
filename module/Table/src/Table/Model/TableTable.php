<?php
namespace Table\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class TableTable
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

    public function getTables($rid)
    {
        $rid  = (int) $rid;

        $adapter = $this->tableGateway->getAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from('dtable');
        $select->where('rid = ' . $rid);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }
    
    public function getTable($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveTable(Table $table)
    {
        $data = array(
            'rid' => $table->rid,
            'num' => $table->num,
            'name' => $table->name,
            'status' => $table->status,
            'note' => $table->note,
        );

        $id = (int) $table->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTable($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Table id does not exist');
            }
        }
    }

    public function deleteTable($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>