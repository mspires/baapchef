<?php
namespace Membership\Model;

use Zend\Db\TableGateway\TableGateway;
use Membership\Model\AddressTable;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class MembershipTable
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

    public function getMemberships($cid)
    {
        $adapter = $this->tableGateway->getAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from('membership');
        $select->join(array('C' => 'restaurant'),
                 'rid = C.id',
                 array('restaurantName' => 'name','restaurantPhone' => 'phone'),
                 $select::JOIN_LEFT);
        $select->join(array('D' => 'customer'),
                'cid = D.id',
                array('customerName' => 'name','customerPhone' => 'phone'),
                $select::JOIN_LEFT);
        
        $select->where(array('cid' => $cid));
        
        $resultSet = $this->tableGateway->selectWith($select);


        return $resultSet;
    }
    
    public function getMembershipsFromRestaurant($rid)
    {
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('membership');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'customer'),
            'cid = D.id',
            array('customerName' => 'name'),
            $select::JOIN_LEFT);
    
        $select->where(array('rid' => $rid));
        
        $resultSet = $this->tableGateway->selectWith($select);
    
    
        return $resultSet;
    }
    
    public function getMembership($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveMembership(Membership $membership)
    {
        $data = array(
            'rid'    => $membership->rid,
            'cid'    => $membership->cid,
            'level'  => $membership->level,
            'status' => $membership->status,
        );

        $id = (int) $membership->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getMembership($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteMembership($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>