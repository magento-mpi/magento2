<?php
/**
 *  -- Upload lib
 *
 * @file        Upload.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Varien_File_Uploader
{
    protected $uploader = null;

    protected $uploadedFile = null;

    function __construct($file=null)
    {
        $this->newUploader($file);
    }

    public function upload($destination, $newFileName=null)
    {
        #
    }

    public function uploadRollback()
    {
    
    }

    public function copy($sourceFileName, $destination, $newFileName=null)
    {
        #
    }

    public function remove($fileName)
    {
        #
    }

    public function move($sourceFileName, $destination, $newFileName=null)
    {
        #
    }

    public function renameFile($newFileName)
    {
        #
    }

    public function changeExtension($newExtension)
    {
        #
    }

    public function getFileName()
    {
    
    }

    public function getFileMime()
    {
        #
    }

    public function getFileSize()
    {
        #
    }

    public function isUploaded()
    {
        #
    }

    public function isProcessed()
    {
        #
    }

    protected function setUploadedFile($file=null)
    {
        $this->uploadedFile = $file;
    }

    protected function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    protected function setUploader($uploader=null)
    {
        $this->uploader = $uploader;
    }

    protected function getUploader()
    {
        return $this->uploader;
    }

    protected function newUploader($file=null)
    {
        require_once("lib/tmp/upload.php");
        $this->setUploader( new Upload( $this->setUploadedFile($file) ) );
    }

    function __destruct()
    {
        $this->uploader->Clean();
    }

}
 
// ft:php
// fileformat:unix
// tabstop:4
?>
