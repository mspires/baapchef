<?php
namespace Order\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class OrderTable
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

    public function getOrderesTo($rid)
    {
        $rid  = (int) $rid;
    
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('orderbox');
        $select->join(array('B' => 'customer'),
            'cid = B.id',
            array('cname' => 'name'),
            $select::JOIN_LEFT);
    
        $select->where('rid = ' . $rid);
    
        $resultSet = $this->tableGateway->selectWith($select);
    
        return $resultSet;
    }
    
    public function getOrderesToNow($rid)
    {
        $rid  = (int) $rid;
    
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('orderbox');
        $select->join(array('B' => 'customer'),
            'cid = B.id',
            array('cname' => 'name'),
            $select::JOIN_LEFT);
        
        $select->where(array('rid'=>$rid));
        $ordered = date('Y-m-d H:i:s', strtotime("-2 hours"));
        
        $select->where->greaterThan('orderdate', $ordered);
            
        
    
        $resultSet = $this->tableGateway->selectWith($select);
    
        return $resultSet;
    }
        
    public function getOrderesFrom($cid)
    {
        $cid  = (int) $cid;

        $adapter = $this->tableGateway->getAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from('orderbox');
        $select->where('cid = ' . $cid);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    public function getOrder($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveOrder(Order $order)
    {
        $data = array(
            'rid' => $order->rid,
            'cid' => $order->cid,
            'ordertype' => $order->ordertype,
            'scheduledate' => $order->scheduledate,
            'takeoutdate' => $order->takeoutdate,
            'rstate'  => $order->rstate,
            'cstate'  => $order->cstate,
        );

        $id = (int) $order->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getOrder($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
        
        return $id;
    }

    public function changeROrderState($id,$state)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $sql = new Sql($adapter);
        $update = $sql->update('orderbox')
            ->set(array('rstate' => $state ))
            ->where(array('id'=>$id));
        
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    
    public function deleteOrder($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>
