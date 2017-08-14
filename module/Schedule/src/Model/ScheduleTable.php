<?php
namespace Schedule\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ScheduleTable
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

    public function getSchedules()
    {
        $resultSet = $this->tableGateway->select(function(Select $select){
            //$select->order('createdate DESC')->limit(4);
        });

        return $resultSet;
    }
    
    public function getSchedule($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveSchedule(Schedule $schedule)
    {
        $data = array(
            'rid' => $schedule->rid,
            'name' => $schedule->name,
            'note' => $schedule->note,
        );

        $id = (int) $schedule->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getSchedule($id)) {
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