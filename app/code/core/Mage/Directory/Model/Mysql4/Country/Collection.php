<?php
/**
 * Country collection
 *
 * @package    Mage
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Model_Mysql4_Country_Collection extends Varien_Data_Collection_Db
{
    protected $_countryTable;
    protected $_defaultCountry;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('directory_read'));
        
        $this->_countryTable = Mage::getSingleton('core/resource')->getTableName('directory/country');
        $countryNameTable = Mage::getSingleton('core/resource')->getTableName('directory/country_name');
        
        $lang = Mage::getSingleton('core/store')->getLanguageCode();
        
        $this->_sqlSelect->from($this->_countryTable);
        $this->_sqlSelect->join($countryNameTable, "$countryNameTable.country_id=$this->_countryTable.country_id AND $countryNameTable.language_code='$lang'");
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('directory/country'));
    }
    
    public function loadByStore($defaultCountryId=false)
    {
        $allowCountries = (string)Mage::getSingleton('core/store')->getConfig('core/allowCountries');
        if (!empty($allowCountries)) {
            $this->addFilter('countries', "$this->_countryTable.country_id IN ($allowCountries)", 'string');
        }

        $this->load();
        
        if (empty($defaultCountryId)) {
            $defaultCountryId = (string)Mage::getSingleton('core/store')->getConfig('core/defaultCountry');
        }
        $this->_defaultCountry = $this->getItemById($defaultCountryId);

        return $this;
    }
    
    public function getDefault($usedId=false)
    {
        if($usedId) {
            return $this->getItemById($usedId);
        }
        return $this->_defaultCountry ? $this->_defaultCountry : Mage::getResourceModel('directory/country');
    }
    
    public function getItemById($countryId)
    {
        foreach ($this->_items as $country) {
            if ($country->getCountryId() == $countryId) { 
                return $country;
            }
        }
        return Mage::getResourceModel('directory/country');
    }
    
    public function toHtmlOptions($default=false)
    {
        $out = '';
        if(!$default) {
            $default = $this->getDefault()->getCountryId();
        }
        foreach ($this->getItems() as $index => $item) {
            $out.='<option value="'.$item->countryId.'"';
            if ($default == $item->countryId) {
                $out.=' selected';
            }
            $out.='>' . $item->name;
            $out.="</option>\n";
        }
         
        return $out;
    }
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('country_id', 'name', array('title'=>'iso2_code'));
    }
}