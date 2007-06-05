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
    
    function __construct($file)
    {
        if ($file) {
            $this->_file = $file;
        }
    }

    public function save($destinationFolder, $newFileName=null)
    {
        $destFile = $destinationFolder;
        if( isset($newFileName) ) {
            $destFile.= DIRECTORY_SEPARATOR.$newFileName;
        }
        return move_uploaded_file($this->_file, $destFile);
    }
    
    public function checkMimeType()
    {
        
    }
}