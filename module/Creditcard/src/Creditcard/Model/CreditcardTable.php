<?php
namespace Creditcard\Model;

use Zend\Db\TableGateway\TableGateway;
use Creditcard\Model\AddressTable;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class CreditcardTable
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
    
    public function getCreditcards($type, $id)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('creditcard');
        $select->where(array('type' => $type, 'tid' => $id));
        
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
    }
        
    public function getCreditcard($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }   

    public function saveCreditcard(Creditcard $creditcard)
    {
        $data = array(
            'tid' => $creditcard->tid,
            'type'  => $creditcard->type,
            'number' => $creditcard->number,
            'name' => $creditcard->name,
            'year' => $creditcard->year,
            'month' => $creditcard->month,
            'cvs' => $creditcard->cvs,
        );

        $id = (int) $creditcard->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCreditcard($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteCreditcard($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
