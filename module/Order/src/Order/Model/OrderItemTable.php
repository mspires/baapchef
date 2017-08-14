<?php
namespace Order\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class OrderItemTable
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
    
    public function getOrderItems($orderid)
    {
        $orderid  = (int) $orderid;
    
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('orderitem');
        $select->join(array('B' => 'dish'),
            'orderitem.dishid = B.id',
            array('rid' => 'rid','dishname' => 'name','price' => 'price'),
            $select::JOIN_LEFT);
        $select->join(array('C' => 'restaurant'),
            'B.rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'dishpicture'),
            'orderitem.dishid = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
    
    
        $select->where('orderid = ' . $orderid);
    
        $resultSet = $this->tableGateway->selectWith($select);
    
        return $resultSet;
    }
    
    
    public function getOrderItemsTo($rid)
    {
        $rid  = (int) $rid;
    
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $subselect = $sql->select();
        $select = $sql->select();
        
        $subselect->from('orderbox', array('dishid' => 'dishid'))->where('rid = ' . $rid);
        
        $select->from('orderitem');
        
        $select->join(array('B' => 'dish'),
            'orderitem.dishid = B.id',
            array('rid' => 'rid','dishname' => 'name','price' => 'price'),
            $select::JOIN_LEFT);
        $select->join(array('C' => 'restaurant'),
            'B.rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'dishpicture'),
            'orderitem.dishid = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
        
    
        $select->where('dishid IN (?)', $subselect);
        
        $resultSet = $this->tableGateway->selectWith($select);
    
        return $resultSet;
    }
       
    public function getOrderItem($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function saveOrderItem(OrderItem $orderitem)
    {
        $data = array(
            'orderid' => $orderitem->orderid,
            'dishid' => $orderitem->dishid,
            'state'  => $orderitem->state,
            'qty'    => $orderitem->qty,
            'note'   => $orderitem->note,
        );
    
        $id = (int) $orderitem->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getOrderItem($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }
    
    public function deleteCartItem($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
    
    public function deleteOrderItems($orderid)
    {
        $this->tableGateway->delete(array('orderid' => (int) $orderid));
    }
}

?>