<?php

interface Mage_Install_Model_Client_Interface
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