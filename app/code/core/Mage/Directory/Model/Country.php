<?php
/**
 * Country
 *
 * @package    Mage
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Model_Country extends Mage_Core_Model_Abstract  
{
    public function __construct() 
    {
        parent::__construct();
        $this->_setResource('directory/country');
        $this->setIdFieldName($this->getResource()->getIdFieldName());
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