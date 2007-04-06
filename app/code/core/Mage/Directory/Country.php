<?php
/**
 * Country
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Country extends Varien_DataObject 
{
    public function __construct($country) 
    {
        parent::__construct($country);
    }
    
    public function load()
    {
        
    }
    
    public function getRegions()
    {
        
    }
    
}