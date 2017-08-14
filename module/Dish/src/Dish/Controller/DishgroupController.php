<?php
/**
 * DishgroupgroupController
 *
 * @author
 *
 * @version
 *
 */
namespace Dish\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dish\Model\Dishgroup;
use Dish\Form\DishgroupForm;

class DishgroupController extends AbstractActionController
{
    protected $dishgroupTable;

    public function indexAction()
    {
        return new ViewModel(array(
            'dishgroups' => $this->getDishgroupTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $form = new DishgroupForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $dishgroup = new Dishgroup();
            $form->setInputFilter($dishgroup->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $dishgroup->exchangeArray($form->getData());
                $this->getDishgroupTable()->saveDishgroup($dishgroup);

                // Redirect to list of albums
                return $this->redirect()->toRoute('dishgroup');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dishgroup', array(
                'action' => 'add'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $dishgroup = $this->getDishgroupTable()->getDishgroup($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('dishgroup', array(
                'action' => 'index'
            ));
        }

        $form  = new DishgroupForm();
        $form->bind($dishgroup);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dishgroup->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getDishgroupTable()->saveDishgroup($dishgroup);

                // Redirect to list of albums
                return $this->redirect()->toRoute('dishgroup');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dishgroup');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getDishgroupTable()->deleteDishgroup($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('dishgroup');
        }

        return array(
            'id'    => $id,
            'dishgroup' => $this->getDishgroupTable()->getDishgroup($id)
        );
    }
     
    public function getDishgroupTable()
    {
        if (!$this->dishgroupTable) {
            $sm = $this->getServiceLocator();
            $this->dishgroupTable = $sm->get('Dish\Model\DishgroupTable');
        }
        return $this->dishgroupTable;
    }
}
