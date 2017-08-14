<?php
namespace Restaurant\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

use GoogleMapsTools\Api\Geocode;
use GoogleMapsTools\Api;

class AddressTable
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

    public function getAddresses($type, $tid)
    {
        $tid  = (int) $tid;
        
        $adapter = $this->tableGateway->getAdapter();
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('address');
        $select->where(array('type' => $type, 'tid' => $tid));
        
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
    }

    public function getAddressByType($type, $tid)
    {
        $tid  = (int) $tid;
    
        $adapter = $this->tableGateway->getAdapter();
    
        $sql = new Sql($adapter);
        $select = $sql->select();
    
        $select->from('address');
        $select->where(array('type' => $type, 'tid' => $tid));
    
        $resultSet = $this->tableGateway->selectWith($select);
    
        $row = $resultSet->current();
    
        return $row;
    }
        
    public function getAddress($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function saveAddress(Address $address)
    {
        $geocode = new Geocode($address->toString());
        
        $geocode->execute();
        $point = $geocode->getFirstPoint();
        
        $latitude = $point->getLatitude();
        $longitude =  $point->getLongitude();
                
        $data = array(
            'type' => $address->type,
            'tid' => $address->tid,
            'address1' => $address->address1,
            'address2' => $address->address2,
            'city' => $address->city,
            'state' => $address->state,
            'zip' => $address->zip,
            'country'  => $address->country,
            'lat' => $latitude,
            'lng' =>$longitude,
        );
    
        $id = (int) $address->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAddress($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Address id does not exist');
            }
        }
    }
    
    public function deleteAddress($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }    
}

?>