<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Upload for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Upload\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\File\Transfer\Adapter;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class UploadController extends AbstractActionController
{
    public function indexAction()
    {
       return array();
    }

    public function uploaddragfileAction()
    {
        $valid = false;
        $url = "";
        $message = "";
        
        if ($this->getRequest()->isPost())
        {
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $destination = sprintf("%s\%09d",'./data/data',1);
            $adapter->setDestination($destination);
        
            $files  = $adapter->getFileInfo();
            foreach($files as $file => $fileInfo) {
            
                $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                
                $newFilename = date('YmdHi') . '-' . uniqid() . '.' . $ext;
                
                $adapter->addFilter('Rename', array(
                    'target' => $newFilename,
                    'overwrite'  => true
                ));
                
                if (!$adapter->receive($fileInfo['name'])) {

                    $messages = $adapter->getMessages();
                }
                else {
                    
                    //list($width, $height) = getimagesize("C:\xampp\htdocs\baapchef\public\data\" . $newFilename);     
                    $imagePath = sprintf("./data/data/%09d/%s", 1, $newFilename);
                    
                    $valid = true;
                    $url = $newFilename;
                }
            }            
        }
        
        $data = array(  'valid'=>$valid,
            'url'=>htmlentities($url),
            'message'=>$message
        );
        
        $result = new JsonModel($data);

        return $result;
    }   
}