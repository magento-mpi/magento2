<?php
/**
 * File upload class
 *
 * @package     Varien
 * @subpackage  File
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */

class Varien_File_Uploader
{
    protected $_file;
    protected $_fileMimeType;
    protected $_uploadType;
    protected $_uploadedFileName;
    protected $_uploadedFileDir;
    protected $_allowCreateFolders = true;
    protected $_allowRenameFiles = false;
    protected $_enableFilesDispersion = false;
    protected $_dispretionPath = null;

    const SINGLE_STYLE = 0;
    const MULTIPLE_STYLE = 1;
    
    function __construct($fileId)
    {
        $this->_setUploadFileId($fileId);
        if( !file_exists($this->_file['tmp_name']) ) {
            throw new Exception('File was not uploaded.');
        } 
    }

    public function save($destinationFolder, $newFileName=null)
    {
        if( !is_writable($destinationFolder) ) {
            throw new Exception('Destination folder is not writable or does not exists.');
        }

        $destFile = $destinationFolder;
        $fileName = ( isset($newFileName) ) ? $newFileName : $this->_file['name'];

        if( $this->_enableFilesDispersion ) {
            $this->setAllowCreateFolders(true);
            $char = 0;
            while( ($char < 2) && ($char < strlen($fileName)) ) {
                $this->_dispretionPath.= DIRECTORY_SEPARATOR . $fileName[$char];
                $char ++;
            }
            $destFile.= $this->_dispretionPath;
        }

        if( $this->_allowRenameFiles ) {
            $fileName = $this->_renameDestinationFile($destFile.DIRECTORY_SEPARATOR.$fileName);
        }

        if( $this->_allowCreateFolders ) {
            $this->_createDestinationFolder($destFile);
        }

        $destFile.= DIRECTORY_SEPARATOR . $fileName;

        $result = move_uploaded_file($this->_file['tmp_name'], $destFile);
        if( $result ) {
            chmod($destFile, 0777);
            $this->_uploadedFileName = ( $this->_enableFilesDispersion ) ? $this->_dispretionPath . DIRECTORY_SEPARATOR . $fileName : $fileName;
            $this->_uploadedFileDir = $destinationFolder;
        } else {
            return $result;
        }
    }
    
    public function checkMimeType($validTypes=Array())
    {
        if( count($validTypes) > 0 ) {
            if( !in_array($this->_getMimeType(), $validTypes) ) {
                return false;
            } 
        }
        return true;
    }

    public function getUploadedFileName()
    {
        return $this->_uploadedFileName;
    }

    public function setAllowCreateFolders($flag)
    {
        $this->_allowCreateFolders = $flag;
    }
    
    public function setAllowRenameFiles($flag)
    {
        $this->_allowRenameFiles = $flag;
    }

    public function setFilesDispersion($flag)
    {
        $this->_enableFilesDispersion = $flag;
    }

    private function _getMimeType()
    {
        return $this->_file['type'];
    }

    private function _setUploadFileId($fileId)
    {
        preg_match("/^(.*?)\[(.*?)\]$/", $fileId, $file);

        array_shift($file);
        if( (count($file[0]) > 0) && (count($file[1]) > 0) ) {
            $this->_uploadType = self::MULTIPLE_STYLE;

            $fileAttributes = $_FILES[$file[0]];
            $tmp_var = array();
            
            foreach( $fileAttributes as $attributeName => $attributeValue ) {
                $tmp_var[$attributeName] = $attributeValue[$file[1]];
            }
            
            $fileAttributes = $tmp_var;
            $this->_file = $fileAttributes;
        } elseif( count($fileId) > 0 ) {
            $this->_uploadType = self::SINGLE_STYLE;
            $this->_file = $_FILES[$fileId];
        } elseif( $fileId == '' ) {
            throw new Exception('Invalid parameter given. A valid $_FILES[] identifier is expected.');
        }
    }

    private function _createDestinationFolder($destinationFolder)
    {
        if( !$destinationFolder ) {
            return;
        }

        $path = explode(DIRECTORY_SEPARATOR, $destinationFolder);
        $newPath = null;
        $oldPath = null;
        foreach( $path as $key => $directory ) {
            $newPath.= ( $newPath != DIRECTORY_SEPARATOR ) ? DIRECTORY_SEPARATOR . $directory : $directory;
            if( is_dir($newPath) ) {
                $oldPath = $newPath;
                continue;
            } else {
                if( is_writable($oldPath) ) {
                    mkdir($newPath, 0777);
                } else {
                    throw new Exception("Unable to create directory '{$newPath}'. Access forbidden.");
                }
            }
            $oldPath = $newPath;
        }
    }

    private function _renameDestinationFile($destFile)
    {
        $fileInfo = pathinfo($destFile);
        if( file_exists($destFile) ) {
            $index = 1;
            $baseName = $fileInfo['filename'] . '.' . $fileInfo['extension'];
            while( file_exists($fileInfo['dirname'] . DIRECTORY_SEPARATOR . $baseName) ) {
                $baseName = $fileInfo['filename']. '_' . $index . '.' . $fileInfo['extension'];
                $index ++;
            }
            $destFileName = $baseName;
        } else {
            return $fileInfo['basename'];
        }

        return $destFileName;
    }
}
