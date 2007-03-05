<?php

/**
 * Ecom Page Module
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 04:06:00 EET 2007
 */

class Ecom_Test_Setup extends Ecom_Core_Setup_Abstract 
{    
    /**
     * Load Module
     * 
     * @param     none
     * @return    none
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function loadFront()
    {
        Ecom::addObserver('initLayout', array($this, 'initLayout'));
    }
    
    public function initLayout()
    {
        $updateLayout = array(':test.initLayout',
            array('#catalog.leftnav.byproduct', array('>setViewName', 'Ecom_Test', 'list')),
        );
        Ecom_Core_Block::loadArray($updateLayout);
    }
}// Class Ecom_Page_Setup END