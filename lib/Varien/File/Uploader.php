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

    const SINGLE_STYLE = 0;
    const MULTIPLE_STYLE = 1;
    
    function __construct($file)
    {
        $file = $_FILES[$file];

        if( !is_array($file) ) {
            throw new Exception("File was not uploaded.");
        } elseif( is_array($file['size']) ) {
            $this->_uploadType = self::MULTIPLE_STYLE;

            $tmp_var = array();
            foreach( $file as $k => $l ) {
                foreach( $l as $i => $v ) {
                    $tmp_var[$k] = $v;
                }
            }
            $file = $tmp_var;
        } elseif( intval($file['size']) > 0 ) {
            $this->_uploadType = self::SINGLE_STYLE;
        }

        if( !file_exists($file['tmp_name']) ) {
            throw new Exception("File was not uploaded.");
        } else {
            $this->_file = $file;
        }
    }

    public function save($destinationFolder, $newFileName=null)
    {
        $destFile = $destinationFolder;
        if( isset($newFileName) ) {
            $fileName = $newFileName;
        } else {
            $fileName = $this->_file['name'];
        }

        $destFile.= DIRECTORY_SEPARATOR.$fileName;

        $result = move_uploaded_file($this->_file['tmp_name'], $destFile);
        if( $result ) {
            chmod($destFile, 0777);
            $this->_uploadedFileName = $fileName;
            $this->_uploadedFileDir = $destinationFolder;
        } else {
            return $result;
        }
    }
    
    public function checkMimeType()
    {
        
    }

    public function getUploadedFileName()
    {
        return $this->_uploadedFileName;
    }
}
