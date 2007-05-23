<?php

class Mage_Install_Model_Client_File extends Mage_Install_Model_Client_Abstract
{
    /**
     * Initialize a connection
     *
     */
    public function init();
    
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
     * Load a file
     *
     */
    public function load();
    
    /**
     * Save a file
     *
     */
    public function save();
    
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