<?php
namespace Reward\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class RewardTable
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
        
        $select->from('reward');
        $select->join(array('D' => 'rewardpicture'),
            'reward.id = D.rewardid',
            array('filename'),
            $select::JOIN_LEFT);
        
        $resultSet = $this->tableGateway->selectWith($select);
        

        return $resultSet;
    }

    public function getRewards($id)
    {
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('reward');
        $select->join(array('D' => 'rewardpicture'),
            'reward.id = D.rewardid',
            array('filename'),
            $select::JOIN_LEFT);
    
        $select->where('reward.rid = ' . $id);
    
        $resultSet = $this->tableGateway->selectWith($select);
    
    
        return $resultSet;
    }
    
    public function getReward($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $id  = (int) $id;
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('reward');
        $select->join(array('D' => 'rewardpicture'),
            'reward.id = D.rewardid',
            array('filename'),
            $select::JOIN_LEFT);
        $select->where('reward.id = ' . $id);
        
        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveReward(Reward $reward)
    {
        $data = array(
            'name' => $reward->name,
            'price' => $reward->price,
            'note'  => $reward->note,
        );

        $id = (int) $reward->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
            
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            
            $insert = $sql->insert('rewardpicture');
            $insert->values(array('rid'=>$reward->rid,
                'rewardid'=>$id,
                'filename'=>$reward->filename,
            ));
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            
        } else {
            if ($this->getReward($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Reward id does not exist');
            }
        }
    }

    public function deleteReward($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
