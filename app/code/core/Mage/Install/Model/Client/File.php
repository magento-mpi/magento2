<?php

/**
 * Filesystem client
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Mage
 * @subpackage  Install
 * @link        http://var-dev.varien.com/wiki/doku.php?id=magento:api:mage:core:config
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Install_Model_Client_File extends Mage_Install_Model_Client_Abstract
{
    /**
     * Save initial working directory
     *
     * @var string
     */
    protected $_iwd;
    
    /**
     * Use virtual current working directory for application integrity
     *
     * @var string
     */
    protected $_cwd;
    
    /**
     * Open a connection
     *
     * @return boolean
     */
    public function open()
    {
        $this->_iwd = getcwd();
        $this->_cwd = getcwd();
        return true;
    }
    
    /**
     * Close a connection
     * 
     * @return boolean
     */
    public function close()
    {
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
        chdir($this->_cwd);
        $result = mkdir($dir, $mode, $recursive);
        chdir($this->_iwd);
        return $result;
    }
    
    /**
     * Delete a directory
     *
     * @param string $dir
     * @return boolean
     */
    public function rmdir($dir)
    {
        chdir($this->_cwd);
        $result = rmdir($dir);
        chdir($this->_iwd);
        return $result;
    }
    
    /**
     * Get current working directory
     *
     * @return string
     */
    public function pwd()
    {
        return $this->_cwd;
    }
    
    /**
     * Change current working directory
     *
     * @param string $dir
     * @return boolean
     */
    public function cd($dir)
    {
        if (chdir($dir)) {
            chdir($this->_iwd);
            $this->_cwd = realpath($dir);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Read a file
     *
     * @param string $filename
     * @return string
     */
    public function read($filename)
    {
        chdir($this->_cwd);
        $result = file_get_contents($filename);
        chdir($this->_iwd);
        return $result;
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
        chdir($this->_cwd);
        $result = file_put_contents($filename, $data);
        chdir($this->_iwd);
        return $result;
    }
    
    /**
     * Delete a file
     *
     * @param string $filename
     * @return boolean
     */
    public function rm($filename)
    {
        chdir($this->_cwd);
        $result = unlink($filename);
        chdir($this->_iwd);
        return $result;
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
        chdir($this->_cwd);
        $result = rename($from, $to);
        chdir($this->_iwd);
        return $result;
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
        chdir($this->_cwd);
        $result = chmod($filename, $mode);
        chdir($this->_iwd);
        return $result;
    }
}