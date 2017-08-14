<?php
namespace Agent\Model;

use Zend\Db\TableGateway\TableGateway;
use Auth\Model\Role;

use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

use Auth\Utility\UserPassword;

class AgentTable
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
    
    public function getAgent($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function saveAgent(Agent $agent)
    {
        $data = array(
            'name' => $agent->name,
            'email'  => $agent->email,
            'userid'  => $agent->email,
            'phone' => $agent->phone,
            'level' => $agent->level,
            'status' => $agent->status,
        );
    
        $id = (int) $agent->id;
        if ($id == 0) {

            if($agent->password == null)
            {
                $userPassword = new UserPassword();
                $data['password'] = $userPassword->generate(8);
            }   
                    
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
            
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            
            $email = $data['email'];
            $phone = $data['phone'];
            $password = $userPassword->generatePIN(4);
            
            $insert = $sql->insert('users');
            $insert->values(array('usertype'=>'Agent',
                'rid'=>$id,
                'name'=>'admin',
                'userid'=>'Admin',
                'email'=>$email,
                'password'=>$password,
                'phone'=>$phone,
                'status'=>'Y' ));
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            
            $userid = $adapter->getDriver()->getLastGeneratedValue();
            
            $insert = $sql->insert('user_role');
            $insert->values(array('user_id'=>$userid, 'role_id' => ROLE::ROLE_ADMIN ));
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            
            $destination = sprintf('%s/%09d/','./data/data/agent',$id);
            
            if (!file_exists($destination)) {
            
                mkdir($destination, 0777, true);
            }
            
            $logo = sprintf('./data/data/agent/%09d/%s.png', $id,'agent');
            copy('./data/data/agent/agent.png', $logo);            
        } else {
            if ($this->getAgent($id)) {
                
                if($agent->password != null)
                {
                    $data['password'] = $agent->password;
                }
                
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }
    
    public function deleteAgent($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }    
}

?>