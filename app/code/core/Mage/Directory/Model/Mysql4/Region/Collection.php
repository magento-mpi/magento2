<?php
/**
 * Country collection
 *
 * @package    Mage
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Model_Mysql4_Region_Collection extends Varien_Data_Collection_Db
{
    protected $_regionTable;
    protected $_regionNameTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('directory_read'));
        
        $this->_regionTable     = Mage::getSingleton('core/resource')->getTableName('directory/country_region');
        $this->_regionNameTable = Mage::getSingleton('core/resource')->getTableName('directory/country_region_name');
        
        $lang = Mage::getSingleton('core/store')->getLanguageCode();
        
        $this->_sqlSelect->from($this->_regionTable);
        $this->_sqlSelect->join($this->_regionNameTable, "$this->_regionNameTable.region_id=$this->_regionTable.region_id AND $this->_regionNameTable.language_code='$lang'");
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('directory/region'));
    }
    
    public function addCountryFilter($countryId)
    {
        $countryId = (int) $countryId;
        $this->addFilter('country', "$this->_regionTable.country_id=$countryId", 'string');
        return $this;
    }
    
    public function toOptionArray()
    {
        return parent::_toOptionArray('region_id', 'name');
    }
}