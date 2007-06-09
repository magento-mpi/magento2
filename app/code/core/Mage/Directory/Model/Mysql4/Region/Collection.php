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
        parent::__construct(Mage::registry('resources')->getConnection('directory_read'));
        
        $this->_regionTable     = Mage::registry('resources')->getTableName('directory_resource', 'country_region');
        $this->_regionNameTable = Mage::registry('resources')->getTableName('directory_resource', 'country_region_name');
        
        $lang = Mage::registry('website')->getLanguage();
        
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
    
    public function toHtmlOptions($default=false)
    {
        $out = '';
        foreach ($this->_items as $index => $item) {
            $out.='<option value="'.$item->getRegionId().'"';
            if ($default == $item->getRegionId()) {
                $out.=' selected';
            }
            $out.='>' . $item->name;
            $out.="</option>\n";
        }
         
        return $out;
    }
}