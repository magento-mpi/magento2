<?php

class Mage_Admin_MediaController extends Mage_Core_Controller_Front_Action
{

    public function foldersTreeAction()
    {
        $io = new Varien_Io_File();

        $_cwd = ( $this->getRequest()->getPost('current_directory') == '' ) ? Mage::getBaseDir('upload') : $this->getRequest()->getPost('current_directory');

        $io->open( Array('path' => $_cwd) );
        $directoriesList = $io->ls(Varien_Io_File::GREP_DIRS);
         
        $this->getResponse()->setBody(Zend_Json::encode($directoriesList));
    }
    
    public function filesGridAction()
    {
        $io = new Varien_Io_File();

        $_cwd = ( $this->getRequest()->getPost('current_directory') == '' ) ? Mage::getBaseDir('upload') : trim( $this->getRequest()->getPost('current_directory') );

        $io->open( Array('path' => $_cwd) );
        $filesList = $io->ls(Varien_Io_File::GREP_FILES);
         
        $this->getResponse()->setBody(Zend_Json::encode($filesList));
    }
    
    public function mkdirAction()
    {
        $_cwd = ( $this->getRequest()->getPost('current_directory') == '' ) ? Mage::getBaseDir('upload') : trim( $this->getRequest()->getPost('current_directory') );
        $newDirName = trim( $this->getRequest()->getPost('new_directory') );

        if( $newDirName == '' ) {
            throw new Exception('Unable to create new directory. Invalid directory name.');
        } else {
            $io = new Varien_Io_File();
            $io->open( Array('path' => $_cwd) );
            $io->cd($_cwd);
            if( !$io->mkdir($newDirName) ) {
                throw new Exception('Unable to create new directory.');
            }
        }
    }

    public function rmdirAction()
    {
        $_cwd = ( $this->getRequest()->getPost('current_directory') == '' ) ? Mage::getBaseDir('upload') : trim( $this->getRequest()->getPost('current_directory') );
        $dirName = trim( $this->getRequest()->getPost('directory') );
        $dirName = 'test';

        if( $dirName == '' ) {
            throw new Exception('Unable to remove directory. Invalid directory name.');
        } else {
            $io = new Varien_Io_File();
            $io->open( Array('path' => $_cwd) );
            $io->cd($_cwd);
            if( !$io->rmdir($dirName) ) {
                throw new Exception('Unable to create new directory.');
            }
        }
    }

    public function moveAction()
    {
        $currentObjDirectoryName = $this->getRequest()->getPost('current_object_dir');
        $currentObjName = $this->getRequest()->getPost('current_object');

        $destObjDirectoryName = $this->getRequest()->getPost('destination_object_dir');
        $destObjName = $this->getRequest()->getPost('destination_object');

        if( $currentObjName == '' || $destObjName == '' ) {
            throw new Exception('Unable to move object. Source or destinations object is not specified.');
        }

        $io = new Varien_Io_File();
        $io = open($currentObjDirectoryName);
        $io->mv($currentObjName, $destObjDirectoryName . DIRECTORY_SEPARATOR . $destObjName);
    }

    public function uploadAction()
    {
        $destinationDir = $this->getRequest()->getPost('destination_dir');
        $uploadFile = new Varien_File_Uploader('upload_file');
        $uploadFile->save(Mage::getBaseDir('upload') . DIRECTORY_SEPARATOR . $destinationDir);
    }
}
