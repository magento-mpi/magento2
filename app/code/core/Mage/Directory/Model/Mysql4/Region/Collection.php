<?php
/**
 * Country collection
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Model_Mysql4_Region_Collection extends Varien_Data_Collection_Db
{
    public function __construct($lang='en') 
    {
        parent::__construct(Mage::registry('resources')->getConnection('directory_read'));
        
        $regionTable = Mage::registry('resources')->getTableName('directory', 'country_region');
        $regionNameTable = Mage::registry('resources')->getTableName('directory', 'country_region_name');
        
        $this->_sqlSelect->from($regionTable);
        $this->_sqlSelect->join($regionNameTable, "$regionNameTable.region_id=$regionTable.region_id AND $regionNameTable.language_code='$lang'");
        
        $this->setItemObjectClass(Mage::getConfig()->getResourceModelClassName('directory', 'region'));
    }
    
    public function toHtmlOptions($default=false)
    {
        $out = '';
        foreach ($this->_items as $index => $item) {
            $out.='<option value="'.$item->countryId.'"';
            if ($default == $item->countryId) {
                $out.=' selected';
            }
            $out.='>' . $item->name;
            $out.="</option>\n";
        }
         
        return $out;
    }
}