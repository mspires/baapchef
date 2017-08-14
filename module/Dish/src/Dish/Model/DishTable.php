<?php
namespace Dish\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class DishTable
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
        
        $select->from('dish');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);        
        $select->join(array('D' => 'dishpicture'),
            'dish.id = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
        
        $resultSet = $this->tableGateway->selectWith($select);
        

        return $resultSet;
    }

    public function getDishes($id)
    {
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('dish');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'dishpicture'),
            'dish.id = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
    
        $select->where('dish.rid = ' . $id);
    
        $resultSet = $this->tableGateway->selectWith($select);
    
    
        return $resultSet;
    }
    
    public function getDishesByGroup($id, $groupid)
    {
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('dish');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'dishpicture'),
            'dish.id = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
        
        if($groupid > 0)
        {
            $select->where(array('dish.rid' =>$id, 'dish.groupid' =>$groupid));
        }
        else 
        {
            $select->where('dish.rid = ' . $id);
        }
        
        $resultSet = $this->tableGateway->selectWith($select);
    
    
        return $resultSet;
    }    
    
    public function getDish($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $id  = (int) $id;
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('dish');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('restaurantName' => 'name'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'dishpicture'),
            'dish.id = D.dishid',
            array('filename'),
            $select::JOIN_LEFT);
        $select->where('dish.id = ' . $id);
        
        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveDish(Dish $dish)
    {
        $data = array(
            'rid' => $dish->rid,
            'groupid' => $dish->groupid,
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
            
            $insert = $sql->insert('dishpicture');
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
