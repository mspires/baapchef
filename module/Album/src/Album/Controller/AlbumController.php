<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;          // <-- Add this import
use Album\Form\AlbumForm;       // <-- Add this import

class AlbumController extends AbstractActionController
{
    protected $albumTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));        
    }

    public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        return array('form' => $form);        
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'add'
            ));
        }
        
        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $album = $this->getAlbumTable()->getAlbum($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'index'
            ));
        }
        
        $form  = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());
        
            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($album);
        
                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
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
            return $this->redirect()->toRoute('album');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
        
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }
        
            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }
        
        return array(
            'id'    => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );        
    }
    
    public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
    
}


// namespace Album\Controller;

// use Album\Model\Album;
// use Zend\Mvc\Controller\AbstractRestfulController;
// use Zend\View\Model\JsonModel;

// class AlbumController extends AbstractRestfulController {

    
//     public function getList() {
//         $em = $this
//                 ->getServiceLocator()
//                 ->get('Doctrine\ORM\EntityManager');

//         $results= $em->createQuery('select a, u from Album\Model\Album a join a.artists u')->getArrayResult();

       
//         return new JsonModel(array(
//             'data' => $results)
//         );
//     }

//     public function get($id) {
//         $em = $this
//                 ->getServiceLocator()
//                 ->get('Doctrine\ORM\EntityManager');

//         $album = $em->find('Album\Model\Album', $id);
        
//         $results= $em->createQuery('select a, u, s from Album\Model\Album a join a.artists u join a.songs s where a.id=:id')
//                 ->setParameter("id", $id)
//                 ->getArrayResult();

//         //print_r($results);
        
//         return new JsonModel($results[0]);
//     }

//     public function create($data) {
//         $em = $this
//                 ->getServiceLocator()
//                 ->get('Doctrine\ORM\EntityManager');

//         $album = new Album();
//         $album->setArtist($data['artist']);
//         $album->setTitle($data['title']);

//         $em->persist($album);
//         $em->flush();

//         return new JsonModel(array(
//             'data' => $album->getId(),
//         ));
//     }

//     public function update($id, $data) {
//         $em = $this
//                 ->getServiceLocator()
//                 ->get('Doctrine\ORM\EntityManager');

//         $album = $em->find('Album\Model\Album', $id);
//         $album->setArtist($data['artist']);
//         $album->setTitle($data['title']);

//         $album = $em->merge($album);
//         $em->flush();

//         return new JsonModel(array(
//             'data' => $album->getId(),
//         ));
//     }

//     public function delete($id) {
//         $em = $this
//                 ->getServiceLocator()
//                 ->get('Doctrine\ORM\EntityManager');

//         $album = $em->find('Album\Model\Album', $id);
//         $em->remove($album);
//         $em->flush();

//         return new JsonModel(array(
//             'data' => 'deleted',
//         ));
//     }

// }
