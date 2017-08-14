<?php
namespace Dish\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class DishgroupTable
{
    protected $tableGateway;
    //protected $dbSql;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        //$this->dbSql = new Sql($this->tableGateway->getAdapter());
    }

    public function fetchAll()
    {
        $adapter = $this->tableGateway->getAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from('dishgroup');
        /*
         $select->join(array('C' => 'membership'),
         'rid = C.id',
         array('membershipName' => 'name'),
         $select::JOIN_LEFT);
         $select->join(array('D' => 'dishpicture'),
         'dish.id = D.dishid',
         array('filename'),
         $select::JOIN_LEFT);
        */
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
    }

    public function getDishgroups($rid)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $rid  = (int) $rid;
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('dishgroup');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        
        $select->where('rid = ' . $rid);
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
    }    
    
    public function getDishgroup($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $id  = (int) $id;
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('dishgroup');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        
        $select->where('dishgroup.id = ' . $id);
        
        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveDishgroup(Dishgroup $dishgroup)
    {
        $data = array(
            'rid' => $dishgroup->rid,
            'name' => $dishgroup->name,
            'note'  => $dishgroup->note,
        );

        $id = (int) $dishgroup->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getDishgroup($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteDishgroup($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
