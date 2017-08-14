<?php
namespace Customer\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

use Auth\Utility\UserPassword;

class CustomerTable
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
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getCustomers($rid)
    {
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('customermap');
        $select->join(array('C' => 'customer'),
             'customermap.cid = C.id',
             array('id'=>'id', 'name'=>'name','email'=>'email','phone'=>'phone'),
             $select::JOIN_LEFT);
        $select->join(array('D' => 'membership'),
            'customermap.cid = D.id',
            array('status'=>'status','level'=>'level'),
            $select::JOIN_LEFT);
        
        $select->where(array('customermap.rid'=>$rid));
        
        $resultSet = $this->tableGateway->selectWith($select);
    
    
        return $resultSet;
    }
    
    public function getCustomer($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function isCustomerExist($email)
    {
        $rowset = $this->tableGateway->select(array('email' => $email));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function saveCustomer(Customer $customer)
    {
        $data = array(
            'name' => $customer->name,
            'userid' => $customer->email,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'level' => $customer->level,
            'status' => $customer->status,
        );

        $id = (int) $customer->id;
        if ($id == 0) {

            if($customer->password == null)
            {
                $userPassword = new UserPassword();
                $data['password'] = $userPassword->generate(8);
            }
            else 
            {
                $data['password'] = $customer->password;
            }
            
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
            
            $destination = sprintf('%s/%09d/','./data/data/customer',$id);
            
            if (!file_exists($destination)) {
            
                mkdir($destination, 0777, true);
            }
            
            $logo = sprintf('./data/data/customer/%09d/%s.png', $id,'customer');
            copy('./data/data/customer/customer.png', $logo);
        } else {
            
            $customer = $this->getCustomer($id);
            if ($customer) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Customer id does not exist');
            }
        }
        
        return $id;
    }

    public function deleteCustomer($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
