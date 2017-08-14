<?php
namespace Reservation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ReservationTable
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

    public function getReservations($rid)
    {
        $rid  = (int) $rid;
        $resultSet = $this->tableGateway->select(array('rid' => $rid));
        
        return $resultSet;
    }
    
    public function getReservation($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveReservation(Reservation $reservation)
    {
        $data = array(
            'rid' => $reservation->rid,
            'name' => $reservation->name,
            'note' => $reservation->note,
        );

        $id = (int) $reservation->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getReservation($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dish id does not exist');
            }
        }
    }

    public function deleteEvent($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>