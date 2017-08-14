<?php

namespace Application\Model;

use Zend\Session\SaveHandler\SaveHandlerInterface;

use Zend\Db\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

/**
 * Description of Mysql
 *
 * @author rab
 */
class SessionHandler  implements SaveHandlerInterface
{
    protected $tableGateway;
    
    /**
     * Session Save Path
     *
     * @var string
     */
    protected $sessionSavePath;

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Lifetime
     * @var int
     */
    protected $lifetime;

    /**
     * Constructor
     *
     */
    public function __construct( $tableGateway )
    {

        $this->tableGateway = $tableGateway;
    }


    /**
     * Open the session
     *
     * @return bool
     */
    public function open( $savePath, $name )
    {
        $this->sessionSavePath = $savePath;
        $this->sessionName     = $name;
        $this->lifetime        = ini_get('session.gc_maxlifetime');

        return true;

    }


    /**
     * Close the session
     *
     * @return bool
     */
    public function close()
    {

        return true;
    }


    /**
     * Read the session
     *
     * @param int session id
     * @return string string of the sessoin
     */
    public function read($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('session');
        $select->where(array('id' => $id));
        
        $rowset = $this->tableGateway->selectWith($select);
        
        $row = $rowset->current();
        if (!$row) {
            return '';
        }
        return $row['data'];
    }

    public function  getSession($id, $name)
    {
        $adapter = $this->tableGateway->getAdapter();
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        
        $select->from('session');
        $select->where(array('id'=>$id, 'name' => $name));
        
        $rowset = $this->tableGateway->selectWith($select);
        
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
                
        return true;
    }
    /**
     * Write the session
     *
     * @param int session id
     * @param string data of the session
     */
    public function write($id, $data )
    {
       
        $data   = (string) $data ;

        $dbdata = array(
            'modified' => time(),
            'data'     => $data,
        );
        
        
        if ($this->getSession($id, $this->sessionName) == false) {
            
            $dbdata['lifetime']  = $this->lifetime;
            $dbdata['id']        = $id;
            $dbdata['name']      = $this->sessionName;
            
            $this->tableGateway->insert($dbdata);
        } else {
            $this->tableGateway->update($dbdata, array('id' => $id));
        }
        
        return true;        
    }


    /**
    * Destoroy the session
    *
    * @param int session id
     * @return bool
     */
     public function destroy($id)
     {
         $this->tableGateway->delete(array('id' => $id));
         return true;
    }


    /**
    * Garbage Collector
    *
    * @param int life time (sec.)
    * @return bool
    */
    public function gc( $maxlifetime )
    {
        $this->tableGateway->delete(array('id' => $id));
        return true;
    }

}

