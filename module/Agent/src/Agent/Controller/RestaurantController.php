<?php
namespace Agent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Restaurant\Model\Restaurant;
use Restaurant\Form\RestaurantForm;
use Zend\Session\Container;
/**
 * RestaurantController
 *
 * @author
 *
 * @version
 *
 */
class RestaurantController extends AbstractActionController
{
    protected $restaurantTable;
    protected $addressTable;

    protected $session;
    protected $logger;
    
    public function __construct()
    {
        $this->session = new Container('User');
    }
    
    public function indexAction()
    {
        if ($this->session->offsetExists('agentID')) {

            $method = $this->params()->fromQuery('method', 'get');
    
            return new ViewModel(array(
                'restaurants' => $this->getRestaurantTable()->getRestaurants($this->session->agentID),
            ));
        }
        else
        {
        
            return $this->redirect()->toRoute('agenthome');
        }
    }

    public function addAction()
    {
        $form = new RestaurantForm();
        $form->get('submit')->setValue('Add');
        $form->get('level')->setValue('1');
        $form->get('status')->setValue('N');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $restaurant = new Restaurant();
            $form->setInputFilter($restaurant->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $restaurant->exchangeArray($form->getData());
                $this->getRestaurantTable()->saveRestaurant($restaurant, $this->session->agentID);
    
                return $this->redirect()->toRoute('agrestaurant');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('agrestaurant', array(
                'action' => 'add'
            ));
        }
    
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        //try {
        $restaurant = $this->getRestaurantTable()->getRestaurant($id);
        $addresses = $this->getAddressTable()->getAddresses('restaurant',$id);
        //}
        //catch (\Exception $ex) {
        //    return $this->redirect()->toRoute('restaurant', array(
        //        'action' => 'index'
        //   ));
        //}
    
        $form  = new RestaurantForm();
        $form->bind($restaurant);
        $form->get('submit')->setAttribute('value', 'Edit');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($restaurant->getInputFilter());
            $form->setData($request->getPost());
    
            if ($form->isValid()) {
                $this->getRestaurantTable()->saveRestaurant($restaurant, $this->session->agentID);
    
                // Redirect to list of albums
                return $this->redirect()->toRoute('agrestaurant');
            }
        }
    
        return array(
            'id' => $id,
            'form' => $form,
            'addresses' => $addresses,
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('agrestaurant');
        }
    
        $this->getAlbumTable()->deleteRestaurant($id);
        return $this->redirect()->toRoute('agrestaurant');
    }
        
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurant = $sm->get('Restaurant\Model\RestaurantTable');
        }
        return $this->restaurant;
    }
    
    public function getAddressTable()
    {
        if (!$this->addressTable) {
            $sm = $this->getServiceLocator();
            $this->addressTable = $sm->get('Restaurant\Model\AddressTable');
        }
        return $this->addressTable;
    }
}