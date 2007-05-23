<?php

class Mage_Install_Model_Client_File extends Mage_Install_Model_Client_Abstract
{
    /**
     * Save initial working directory to return when finished working with filesystem
     *
     * @var string
     */
    protected $_initialDir;
    
    /**
     * Open a connection
     *
     * @return boolean
     */
    public function open()
    {
        $this->_initialDir = $this->pwd();
        return true;
    }
    
    /**
     * Close a connection
     * 
     * @return boolean
     */
    public function close()
    {
        $this->cd($this->_initialDir);
        return true;
    }
    
    /**
     * Create a directory
     *
     * @param string $dir
     * @param int $mode
     * @param boolean $recursive
     * @return boolean
     */
    public function mkdir($dir, $mode=0777, $recursive=true)
    {
        return mkdir($dir, $mode, $recursive);
    }
    
    /**
     * Delete a directory
     *
     * @param string $dir
     * @return boolean
     */
    public function rmdir($dir)
    {
        return rmdir($dir);
    }
    
    /**
     * Get current working directory
     *
     * @return string
     */
    public function pwd()
    {
        return getcwd();
    }
    
    /**
     * Change current working directory
     *
     * @param string $dir
     * @return boolean
     */
    public function cd($dir)
    {
        return chdir($dir);
    }

    /**
     * Read a file
     *
     * @param string $filename
     * @return string
     */
    public function read($filename)
    {
        return file_get_contents($filename);
    }
    
    /**
     * Write a file
     *
     * @param string $filename
     * @param string $data
     * @return int|boolean
     */
    public function write($filename, $data)
    {
        return file_put_contents($filename, $data);
    }
    
    /**
     * Delete a file
     *
     * @param string $filename
     * @return boolean
     */
    public function rm($filename)
    {
        return unlink($filename);
    }
    
    /**
     * Rename or move a directory or a file
     *
     * @param string $from
     * @param string $to
     * @return boolean
     */
    public function mv($from, $to)
    {
        return rename($from, $to);
    }
    
    /**
     * Change mode of a directory or a file
     * 
     * @param string $filename
     * @param int $mode
     * @return boolean
     */
    public function chmod($filename, $mode)
    {
        return chmod($filename, $mode);
    }
}