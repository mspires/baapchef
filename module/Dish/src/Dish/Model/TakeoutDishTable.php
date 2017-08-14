<?php
namespace Dish\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;


class TakeoutDishTable
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
    
        $select->from('takeoutdish');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'tdishpicture'),
            'takeoutdish.id = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
    
        $resultSet = $this->tableGateway->selectWith($select);
    
    
        return $resultSet;
    }
    
    public function getDishes($rid)
    {
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('takeoutdish');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'tdishpicture'),
            'takeoutdish.id = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
        
        $select->where('takeoutdish.rid = ' . $rid);
        
        $resultSet = $this->tableGateway->selectWith($select);
    
        return $resultSet;
    }
    
    public function getDish($id)
    {
        $adapter = $this->tableGateway->getAdapter();
    
        $id  = (int) $id;
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('takeoutdish');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'tdishpicture'),
            'takeoutdish.id = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
        $select->where('takeoutdish.id = ' . $id);
    
        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getDishesByQuery($query)
    {
        
        $adapter = $this->tableGateway->getAdapter();
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('takeoutdish');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name', 'tax' => 'tax'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'tdishpicture'),
            'takeoutdish.id = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
        
        if($query != '')
        {
            $select->where($query);
        }
        
        $resultSet = $this->tableGateway->selectWith($select);
        
        
        return $resultSet;        
    }
    
    public function saveDish(TakeoutDish $dish)
    {
        $data = array(
            'rid' => $dish->rid,
            'name' => $dish->name,
            'price' => $dish->price,
            'note'  => $dish->note,
        );
    
        $id = (int) $dish->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
            
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            
            $insert = $sql->insert('tdishpicture');
            $insert->values(array('rid'=>$dish->rid,
                                    'dishid'=>$id,
                                    'filename'=>$dish->filename,
             ));
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            
            
        } else {
            if ($this->getDish($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }
    
    public function deleteDish($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }    
}

?>