<?php
/**
 * Country collection
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Region_Collection extends Mage_Core_Resource_Model_Collection
{
    public function __construct($lang='en') 
    {
        parent::__construct(Mage::getResourceModel('directory'));
        
        $regionTable = $this->_dbModel->getTableName('directory', 'country_region');
        $regionNameTable = $this->_dbModel->getTableName('directory', 'country_region_name');
        
        $this->_sqlSelect->from($regionTable);
        $this->_sqlSelect->join($regionNameTable, "$regionNameTable.region_id=$regionTable.region_id AND $regionNameTable.language_code='$lang'");
        
        $this->setItemObjectClass('Mage_Region_Country');
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