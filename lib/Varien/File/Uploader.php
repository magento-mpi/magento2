<?php
/**
 *  -- Upload lib
 *
 * @file        Upload.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 * @TODO:       copy(), remove(), move(), renameFile(), switch()
 */

class Varien_File_Uploader
{
    protected $uploader = null;

    protected $uploadedFile = null;

    function __construct($file=null)
    {
        $this->newUploader($file);
    }

    public function save($destinationFolder, $newFileName=null)
    {
        if( isset($newFileName) ) {
            $this->uploader->file_new_name_body = $newFileName;
        }
        $this->uploader->Process($destinationFolder);
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
        $this->uploader->file_new_name_ext = $newExtension;
    }

    public function getFileName()
    {
        return $this->uploader->file_src_name;
    }

    public function getFileMime()
    {
        return $this->uploader->file_src_mime;
    }

    public function getFileDstName()
    {
        return $this->uploader->file_dst_name;
    }

    public function getFileSize()
    {
        return $this->uploader->file_src_size;
    }

    public function getDestinationPath()
    {
        return $this->uploader->file_dst_path;
    }

    public function isUploaded()
    {
        return $this->uploader->uploaded;
    }

    public function isProcessed()
    {
        return $this->uploader->processed;
    }

    public function switchToImage($fileName)
    {
        #
    }

    public function getError()
    {
        return $this->uploader->error;
    }

    protected function setUploadedFile($file=null)
    {
        $this->uploadedFile = $file;
        return $this->getUploadedFile();
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
