<?php
namespace System\Model;

use Zend\Db\TableGateway\TableGateway;
use System\Model\PermissionTable;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class PermissionTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
         $adapter = $this->tableGateway->getAdapter();
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('permission');
        $select->join(array('C' => 'resource'),
            'permission.resource_id = C.id',
            array('resource_name' => 'resource_name'),
            $select::JOIN_LEFT);        
        $select->order('permission.id');
        
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
    }

    public function getPermissions($cid)
    {
        $adapter = $this->tableGateway->getAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from('permission');

        $resultSet = $this->tableGateway->selectWith($select);


        return $resultSet;
    }

    public function getPermission($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePermission(Permission $permission)
    {
        $data = array(
            'name' => $permission->name,
            'status' => $permission->status,
        );

        $id = (int) $permission->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPermission($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deletePermission($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>