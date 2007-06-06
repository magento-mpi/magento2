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
    protected $_allowRenameFiles = true;

    const SINGLE_STYLE = 0;
    const MULTIPLE_STYLE = 1;
    
    function __construct($fileID)
    {
        $this->_setUploadFileID($fileID);
        if( !file_exists($this->_file['tmp_name']) ) {
            throw new Exception('File was not uploaded.');
        } 
    }

    public function save($destinationFolder, $newFileName=null)
    {
        if( $this->_allowCreateFolders ) {
            $this->_createDestinationFolder($destinationFolder);
        }

        if( !is_writable($destinationFolder) ) {
            throw new Exception('Destination folder is not writable or does not exists.');
        }

        $destFile = $destinationFolder;
        $fileName = ( isset($newFileName) ) ? $newFileName : $this->_file['name'];

        $destFile.= DIRECTORY_SEPARATOR . $fileName;

        $result = move_uploaded_file($this->_file['tmp_name'], $destFile);
        if( $result ) {
            chmod($destFile, 0777);
            $this->_uploadedFileName = $fileName;
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

    private function _getMimeType()
    {
        return $this->_file['type'];
    }

    private function _setUploadFileID($fileID)
    {
        preg_match("/^(.*?)\[(.*?)\]$/", $fileID, $file);

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
        } elseif( count($fileID) > 0 ) {
            $this->_uploadType = self::SINGLE_STYLE;
            $this->_file = $_FILES[$fileID];
        } elseif( $fileID == '' ) {
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
}
