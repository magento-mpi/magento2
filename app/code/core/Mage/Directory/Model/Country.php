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
    public function __construct() 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('directory/country');
    }
    
    public function load($countryId)
    {
        
    }
    
    public function save()
    {
        
    }
    
    public function getRegions()
    {
        return $this->getLoadedRegionCollection();
    }
    
    public function getLoadedRegionCollection()
    {
        $collection = $this->getRegionCollection();
        $collection->load();
        return $collection;
    }
    
    public function getRegionCollection()
    {
        $collection = Mage::getResourceModel('directory/region_collection');
        $collection->addCountryFilter($this->getId());
        return $collection;
    }
}