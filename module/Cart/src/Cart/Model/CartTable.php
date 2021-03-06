<?php
namespace Cart\Model;

use Zend\Db\TableGateway\TableGateway;

use Cart\Model\CartItem;

use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class CartTable
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

    public function getCarts($cid)
    {
        $cid  = (int) $cid;

        $adapter = $this->tableGateway->getAdapter();

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from('cart');
        $select->where('cid = ' . $cid);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    public function getCart($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveCart(Cart $cart)
    {
        $data = array(
            'rid' => $cart->rid,
            'cid' => $cart->cid,
            'createdate' => $cart->createdate,
            'state'  => $cart->state,
        );

        $id = (int) $cart->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCart($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteCart($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>
