<?php
/**
 * Madia library controller
 *
 * @package    Mage
 * @subpackage Admin
 * @author     Alexander Stadnitski <alexander@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */

class Mage_Admin_MediaController extends Mage_Core_Controller_Front_Action
{

    public function foldersTreeAction()
    {
        $io = new Varien_Io_File();

        $_cwd = ( $this->getRequest()->getParam('node', false) === '::' ) ? Mage::getBaseDir('upload') : $this->getRequest()->getParam('node', false);

        try {
            $io->open( Array('path' => $_cwd) );
            $directoriesList = $io->ls(Varien_Io_File::GREP_DIRS);
        } catch( Exception $e ) {
            $this->getResponse()->setBody(Zend_Json::encode($e->getMessage()));
            return;
        }

        foreach( $directoriesList as $key => $directory ) {
            $directoriesList[$key]['iconCls'] = '.tree-folder-icon';
            $directoriesList[$key]['allowEdit'] = true;
        }

        $this->getResponse()->setBody(Zend_Json::encode($directoriesList));
        return;
    }
    
    public function filesGridAction()
    {
        $io = new Varien_Io_File();

        $_cwd = ( $this->getRequest()->getParam('node', false) == '::' ) ? Mage::getBaseDir('upload') : trim( $this->getRequest()->getParam('node', false) );

        try {
            $io->open( Array('path' => $_cwd) );
            $filesList = $io->ls(Varien_Io_File::GREP_FILES);
        } catch( Exception $e ) {
            $this->getResponse()->setBody(Zend_Json::encode($e->getMessage()));
            return;
        }

        $data['data'] = $filesList;

        $this->getResponse()->setBody(Zend_Json::encode($data));
        return;
    }
    
    public function mkdirAction()
    {
        $_cwd = ( $this->getRequest()->getParam('node', false) == '::' ) ? Mage::getBaseDir('upload') : trim( $this->getRequest()->getParam('node', false) );
        $newDirName = trim( $this->getRequest()->getParam('new_directory', false) );

        if( $newDirName == '' ) {
            $this->getResponse()->setBody(Zend_Json::encode('Unable to create new directory. Invalid directory name.'));
            return;
        } else {
            $io = new Varien_Io_File();
            $io->open( Array('path' => $_cwd) );
            $io->cd($_cwd);
            if( !$io->mkdir($newDirName) ) {
                $this->getResponse()->setBody(Zend_Json::encode('Unable to create new directory.'));
                return;
            }
        }
    }

    public function rmdirAction()
    {
        $_cwd = ( $this->getRequest()->getParam('node', false) == '::' ) ? Mage::getBaseDir('upload') : trim( $this->getRequest()->getParam('node', false) );
        $dirName = trim( $this->getRequest()->getParam('directory', false) );
        $dirName = 'test';

        if( $dirName == '' ) {
            $this->getResponse()->setBody(Zend_Json::encode('Unable to remove directory. Invalid directory name.'));
            return;
        } else {
            $io = new Varien_Io_File();
            $io->open( Array('path' => $_cwd) );
            $io->cd($_cwd);
            if( !$io->rmdir($dirName) ) {
                $this->getResponse()->setBody(Zend_Json::encode('Unable to remove directory.'));
                return;
            }
        }
    }

    public function moveAction()
    {
        $currentObj = $this->getRequest()->getParam('current_object', false);

        $destObj = $this->getRequest()->getParam('destination_object', false);

        if( $currentObj === false || $destObj === false ) {
            $this->getResponse()->setBody(Zend_Json::encode('Unable to move object. Source or destinations object is not specified.'));
            return;
        }

        if( !is_dir($currentObj) ) {
            $pathinfo = pathinfo($currentObj);
            $srcDir = $pathinfo['dirname'];
        } else {
            $srcDir = $currentObj;
        }

        $io = new Varien_Io_File();
        $io->open(Array('path' => $srcDir));
        $io->mv($currentObj, $destObj);
    }

    public function uploadAction()
    {
        $destinationDir = $this->getRequest()->getParam('destination_dir', false);
        $uploadFile = new Varien_File_Uploader('upload_file');
        $uploadFile->save(Mage::getBaseDir('upload') . DIRECTORY_SEPARATOR . $destinationDir);
    }
}
