<?php

/**
 * Input/output client interface
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Varien
 * @subpackage  Io
 * @author      Moshe Gurvich <moshe@varien.com>
 */
interface Varien_Io_Interface
{
    /**
     * Open a connection
     *
     */
    public function open();
    
    /**
     * Close a connection
     *
     */
    public function close();
    
    /**
     * Create a directory
     *
     */
    public function mkdir($dir, $mode=0777, $recursive=true);
    
    /**
     * Delete a directory
     *
     */
    public function rmdir($dir);
    
    /**
     * Get current working directory
     *
     */
    public function pwd();
    
    /**
     * Change current working directory
     *
     */
    public function cd($dir);

    /**
     * Read a file
     *
     */
    public function read($filename, $dest=null);
    
    /**
     * Write a file
     *
     */
    public function write($filename, $src, $mode=null);
    
    /**
     * Delete a file
     *
     */
    public function rm($filename);
    
    /**
     * Rename or move a directory or a file
     *
     */
    public function mv($src, $dest);
    
    /**
     * Chamge mode of a directory or a file
     *
     */
    public function chmod($filename, $mode);

    /**
     * Get list of cwd subdirectories and files
     *
     */
    public function ls($grep=null);
}
