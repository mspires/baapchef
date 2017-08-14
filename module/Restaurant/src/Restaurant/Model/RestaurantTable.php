<?php
namespace Restaurant\Model;

use Zend\Db\TableGateway\TableGateway;
use Restaurant\Model\AddressTable;
use Auth\Model\Role;

use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

use Auth\Utility\UserPassword;

class RestaurantTable
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

    public function getRestaurants($agentid)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('agentmap');
        $select->join(array('C' => 'restaurant'),
            'rid = C.id',
            array('id' => 'id', 'name' => 'name', 'email' => 'email', 'phone' => 'phone'),
            $select::JOIN_LEFT);        
    
        $select->where(array('agentid'=>$agentid));
        
        $resultSet = $this->tableGateway->selectWith($select);
        

        return $resultSet;
    }
        
    public function getRestaurant($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }   

    public function saveRestaurant(Restaurant $restaurant, $agentid = 0)
    {
        $data = array(
            'name' => $restaurant->name,
            'email'  => $restaurant->email,
            'userid'  => $restaurant->email,
            'phone' => $restaurant->phone,
            'level' => $restaurant->level,
            'status' =>$restaurant->status,
            'tax' => $restaurant->tax,
        );

        $id = (int) $restaurant->id;
        if ($id == 0) {

            $userPassword = new UserPassword();
            $data['password'] = $userPassword->generate(8);
            
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
            
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            
            $email = $data['email'];
            $phone = $data['phone'];
            $password = $userPassword->generatePIN(4);
            
            $insert = $sql->insert('users');
            $insert->values(array('usertype'=>'Restaurant',
                'rid'=>$id,
                'name'=>'admin',
                'userid'=>'Admin',
                'password'=>$password,
                'email'=>$email,
                'phone'=>$phone,
                'status'=>'Y' ));
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            
            $userid = $adapter->getDriver()->getLastGeneratedValue();
            
            $insert = $sql->insert('user_role');
            $insert->values(array('user_id'=>$userid, 'role_id' => ROLE::ROLE_ADMIN ));
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            
            $destination = sprintf('%s/%09d','./data/data',$id);
            $destination2 = sprintf('%s/%09d','./data/data/restaurant',$id);
                            
            if (!file_exists($destination)) {
                
                mkdir($destination, 0777, true);
            }
            if (!file_exists($destination2)) {
            
                mkdir($destination2, 0777, true);
            }
            
            $logo = sprintf("./data/data/restaurant/%09d/%s.png", $id,'logo');
            copy("./data/data/restaurant/logo.png", $logo);
            
            if($agentid != 0)
            {
                $insert = $sql->insert('agentmap');
                $insert->values(array('rid'=>$id,'agentid' => $agentid ));              
                
                $statement = $sql->prepareStatementForSqlObject($insert);
                $statement->execute();
            }
            
        } else {
            if ($this->getRestaurant($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteRestaurant($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
