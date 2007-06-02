<?php
/**
 * Country
 *
 * @package    Mage
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Model_Country extends Varien_Object 
{
    public function __construct($country=null) 
    {
        parent::__construct($country);
    }
    
    public function load()
    {
        
    }
    
    public function getRegions()
    {
        $collection = $this->getRegionCollection();
        $collection->load();
        return $collection;
    }
    
    public function getRegionCollection()
    {
        $collection = Mage::getModel('directory_resource', 'region_collection');
        $collection->addCountryFilter($this->getCountryId());
        return $collection;
    }
}