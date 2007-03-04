<?php

/**
 * Ecom Directory Module
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 04:06:00 EET 2007
 */

class Ecom_Directory_Module extends Ecom_Core_Module_Abstract 
{
    /**
     * Module info
     *
     * @var    array
     */
    protected $_info = array(
        'name'      => 'Ecom_Directory',
        'version'   => '0.1.0',
    );
    
    /**
     * Load Module
     * 
     * @param     none
     * @return    none
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function load()
    {
        Ecom::addObserver('initLayout.after', array($this, 'updateLayout'));
    }
    
    /**
     * Run module
     * 
     * @param     none
     * @return    none
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function run()
    {
        Ecom::dispatchEvent(__METHOD__);
    }
    
    public function updateLayout()
    {
        
    }
}// Class Ecom_Page_Module END