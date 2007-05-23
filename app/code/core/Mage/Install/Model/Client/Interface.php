<?php

/**
 * Install and upgrade client interface
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Mage
 * @subpackage  Install
 * @link        http://var-dev.varien.com/wiki/doku.php?id=magento:api:mage:core:config
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Moshe Gurvich <moshe@varien.com>
 */
interface Mage_Install_Model_Client_Interface
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
    public function mkdir();
    
    /**
     * Delete a directory
     *
     */
    public function rmdir();
    
    /**
     * Get current working directory
     *
     */
    public function pwd();
    
    /**
     * Change current working directory
     *
     */
    public function cd();

    /**
     * Read a file
     *
     */
    public function read();
    
    /**
     * Write a file
     *
     */
    public function write();
    
    /**
     * Delete a file
     *
     */
    public function rm();
    
    /**
     * Rename or move a directory or a file
     *
     */
    public function mv();
    
    /**
     * Chamge mode of a directory or a file
     *
     */
    public function chmod();
}