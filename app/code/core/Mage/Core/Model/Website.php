<?php
/**
 * Website
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Website
{
    public function __construct() 
    {
        
    }
    
    public function getId()
    {
        return 1;
    }

    public function getDomain()
    {
        return 'base';
    }

    public function getLanguage()
    {
        return 'en';
    }
}