<?php

/**
 * Filesystem client
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Varien
 * @subpackage  Io
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Varien_Io_File extends Varien_Io_Abstract
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
     * Possible arguments:
     * - path     default current path
     * 
     * @param array $args
     * @return boolean
     */
    public function open(array $args=array())
    {
        $this->_iwd = getcwd();
        $this->cd(!empty($args['path']) ? $args['path'] : $this->_iwd);
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
        $result = @mkdir($dir, $mode, $recursive);
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
        $result = @rmdir($dir);
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
     * Read a file to result, file or stream
     *
     * If $dest is null the output will be returned.
     * Otherwise it will be saved to the file or stream and operation result is returned.
     * 
     * @param string $filename
     * @param string|resource $dest
     * @return boolean|string
     */
    public function read($filename, $dest=null)
    {
        chdir($this->_cwd);
        $result = @file_get_contents($filename);
        chdir($this->_iwd);
        
        if (is_string($dest) || is_resource($dest)) {
            return @file_put_contents($dest, $result);
        } elseif (is_null($dest)) {
            return $result;
        } else {
            return false;
        }
    }
    
    /**
     * Write a file from string, file or stream
     *
     * @param string $filename
     * @param string|resource $src
     * @return int|boolean
     */
    public function write($filename, $src, $mode=null)
    {
        if (is_string($src) && is_readable($src)) {
            $src = realpath($src);
            $srcIsFile = true;
        } elseif (is_string($src) || is_resource($src)) {
            $srcIsFile = false;
        } else {
            return false;
        }
        
        chdir($this->_cwd);
        if ($srcIsFile) {
            $result = @copy($src, $filename);
        } else {
            $result = @file_put_contents($filename, $src);
        }
        if (!is_null($mode)) {
            @chmod($filename, $mode);
        }
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
        $result = @unlink($filename);
        chdir($this->_iwd);
        return $result;
    }
    
    /**
     * Rename or move a directory or a file
     *
     * @param string $src
     * @param string $dest
     * @return boolean
     */
    public function mv($src, $dest)
    {
        chdir($this->_cwd);
        $result = @rename($src, $dest);
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
        $result = @chmod($filename, $mode);
        chdir($this->_iwd);
        return $result;
    }
}