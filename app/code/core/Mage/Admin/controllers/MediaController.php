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
            $result = Array('error' => 1, 'error_message' => $e->getMessage());
            $this->getResponse()->setBody(Zend_Json::encode($result));
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

        $baseDirArr = explode(DIRECTORY_SEPARATOR, Mage::getBaseDir('upload'));
        $_cwdArr = explode(DIRECTORY_SEPARATOR, $_cwd);

        try {
            $io->open( Array('path' => $_cwd) );
            $filesList = $io->ls(Varien_Io_File::GREP_FILES);
        } catch( Exception $e ) {
            $result = Array('error' => 1, 'error_message' => $e->getMessage());
            $this->getResponse()->setBody(Zend_Json::encode($result));
            return;
        }

        foreach( $filesList as $key => $file ) {
            $tmpvar = $_cwdArr;
            $tmpvar[] = $file['text'];

            if( $file['is_image'] === true && $file['size'] > 0 ) {
                $filesList[$key]['url'] = Mage::getBaseUrl().'media/upload/'.join(DIRECTORY_SEPARATOR, array_diff($tmpvar, $baseDirArr));
            } else {
                $filesList[$key]['url'] = Mage::getBaseUrl(array('_type'=>'skin')).'admin/filetypes/'.$file['filetype'].'.png';
            }
        }

        $result = Array('error' => 0, 'data' => $filesList);
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }
    
    public function mkdirAction()
    {
        $_cwd = ( $this->getRequest()->getParam('node', false) == '::' ) ? Mage::getBaseDir('upload') : trim( $this->getRequest()->getParam('node', false) );
        $newDirName = trim( $this->getRequest()->getParam('new_directory', false) );

        if( $newDirName == '' ) {
            $result = Array('error' => 1, 'error_message' => 'Unable to create new directory. Invalid directory name.');
            $this->getResponse()->setBody(Zend_Json::encode($result));
            return;
        } else {
            $io = new Varien_Io_File();
            $io->open( Array('path' => $_cwd) );
            $io->cd($_cwd);
            if( !$io->mkdir($newDirName) ) {
                $result = Array('error' => 1, 'error_message' => 'Unable to create new directory.');
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
        }
        $result = Array('error' => 0);
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function rmAction()
    {
        $destination = ( $this->getRequest()->getParam('node', false) == '::' ) ? Mage::getBaseDir('upload') : trim( $this->getRequest()->getParam('node', false) );

        if( $destination === false ) {
            $result = Array('error' => 1, 'error_message' => 'Unable to remove target. Invalid target name.');
            $this->getResponse()->setBody(Zend_Json::encode($result));
            return;
        } else {
            $io = new Varien_Io_File();
            if( !$io->rmdir($destination) && !$io->rm($destination) ) {
                $result = Array('error' => 1, 'error_message' => 'Unable to remove target.');
                $this->getResponse()->setBody(Zend_Json::encode($result));
                return;
            }
        }
        $result = Array('error' => 0);
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function moveAction()
    {
        $currentObj = $this->getRequest()->getParam('current_object', false);

        $destObj = $this->getRequest()->getParam('destination_object', false);

        if( $currentObj === false || $destObj === false ) {
            $result = Array('error' => 1, 'error_message' => 'Unable to move object. Source or destinations object is not specified.');
            $this->getResponse()->setBody(Zend_Json::encode($result));
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

        $result = Array('error' => 0);
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function uploadAction()
    {
        $destinationDir = $this->getRequest()->getParam('destination_dir', false);
        $result = Array();
        try {
            $uploadFile = new Varien_File_Uploader('upload_file');
            $result = $uploadFile->save(Mage::getBaseDir('upload') . DIRECTORY_SEPARATOR . $destinationDir);
        } catch( Exception $e ) {
            $result['error'] = $e->getMessage();
        }

        $response = new Varien_Object;
        $response->addData($result);
        header("Content-type: text/xml");
        $this->getResponse()->setBody($response->toXml(array(), "file", true, false));
    }

    public function loadSettingsAction()
    {
        $result['error'] = 0;
        $result['root_directory'] = Mage::getBaseDir('upload');
        $result['directory_separator'] = DIRECTORY_SEPARATOR;
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}
