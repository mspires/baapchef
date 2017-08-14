<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

class UtilController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function countryAction()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $sql       = 'SELECT countrycode2, countryname FROM country WHERE countrycode2="US"';
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();
        
        $selectData = array();
        
        foreach ($result as $res) {
            $selectData[] = array('code' => $res['countrycode2'], 'name' =>$res['countryname']);
        }
        
        return new JsonModel(array('countries' => $selectData));        
    }
    
    public function stateAction()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    
        $sql       = 'SELECT statecode, statename FROM state';
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();
    
        $selectData = array();
    
        foreach ($result as $res) {
            $selectData[] = array('code' => $res['statecode'], 'name' =>$res['statename']);
        }
    
        return new JsonModel(array('states' => $selectData));
    }
}