<?php

/**
 * Ecom website
 *
 * @package    Ecom
 * @module     Core
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Website 
{
    static protected  $_websites;
    
    static public function getWebsiteId($code = 'default')
    {
    	return 1;
    }
}