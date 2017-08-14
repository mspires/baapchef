<?php
namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class UserTable
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
        
        $select->from('users');
        $select->join(array('C' => 'user_role'),
            'users.id = C.user_id',
            array('role_id' => 'role_id'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'role'),
            'C.role_id = D.rid',
            array('role_name' => 'role_name'),
            $select::JOIN_LEFT);
        
        $resultSet = $this->tableGateway->selectWith($select);
        

        return $resultSet;
    }

    public function getUseres($type, $rid)
    {
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('users');
        $select->join(array('C' => 'user_role'),
            'users.id = C.user_id',
            array('role_id' => 'role_id'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'role'),
            'C.role_id = D.rid',
            array('role_name' => 'role_name'),
            $select::JOIN_LEFT);
    
        $select->where(array('users.usertype' => $type, 'users.rid' => $rid));
        
        $select->order('role_id DESC');
        
        $resultSet = $this->tableGateway->selectWith($select);
    
    
        return $resultSet;
    }    
    
    public function getUser($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $id  = (int) $id;
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('users');
        $select->join(array('C' => 'user_role'),
            'users.id = C.user_id',
            array('role_id' => 'role_id'),
            $select::JOIN_LEFT);
        $select->join(array('D' => 'role'),
            'C.role_id = D.rid',
            array('role_name' => 'role_name'),
            $select::JOIN_LEFT);
        
        $select->where('users.id = ' . $id);
        
        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveUser(User $user)
    {
        $data = array(
            'usertype' => $user->usertype,
            'rid' => $user->rid,
            'name' => $user->name,
            'userid' => $user->email,
            'email' => $user->email,
            'phone' => $user->phone,
            'password' => $user->password,
            'status' => $user->status,
        );

        $id = (int) $user->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
            
            $adapter = $this->tableGateway->getAdapter();
            
            $sql = new Sql($adapter);
            $insert = $sql->insert('user_role');
            $insert->values(array('user_id'=>$id,'role_id' => $user->role_id ));
            
            //$selectString = $sql->getSqlStringForSqlObject($insert);
            //$adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('id' => $id));
                
                $adapter = $this->tableGateway->getAdapter();
                
                $sql = new Sql($adapter);
                $update = $sql->update('user_role')
                            ->set(array('role_id' => $user->role_id ))
                            ->where(array('user_id'=>$id));
                
                $statement = $sql->prepareStatementForSqlObject($update);
                $statement->execute();        
            } else {
                throw new \Exception('User id does not exist');
            }
        }
    }

    public function deleteUser($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
