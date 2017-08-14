<?php
namespace System\Model;

use Zend\Db\TableGateway\TableGateway;
use System\Model\ResourceTable;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class ResourceTable
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

    public function getRoles($cid)
    {
        $adapter = $this->tableGateway->getAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from('role');

        $resultSet = $this->tableGateway->selectWith($select);


        return $resultSet;
    }

    public function getRole($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveRole(Role $role)
    {
        $data = array(
            'name' => $role->name,
            'status' => $role->status,
        );

        $id = (int) $role->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRole($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteRole($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>