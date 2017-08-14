<?php
namespace Cart\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class CartItemTable
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

    public function getCartItems($cartid)
    {
        $cartid  = (int) $cartid;

        $adapter = $this->tableGateway->getAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('cartitem');
        $select->join(array('B' => 'dish'),
            'cartitem.dishid = B.id',
            array('rid' => 'rid','dishname' => 'name','price' => 'price'),
            $select::JOIN_LEFT);
        $select->join(array('C' => 'restaurant'),
            'B.rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'dishpicture'),
            'cartitem.dishid = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
        

        $select->where('cartid = ' . $cartid);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    public function getCartItem($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveCartItem(CartItem $cartitem)
    {
        $data = array(
            'cartid' => $cartitem->cartid,
            'dishid' => $cartitem->dishid,
            'state'  => $cartitem->state,
            'qty'    => $cartitem->qty,
            'note'   => $cartitem->note,
            'state'  => 0
        );

        $id = (int) $cartitem->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCartItem($id)) {
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
}

?>