<?php
/**
 * Country collection
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Country_Collection extends Mage_Core_Resource_Model_Collection
{
    public function __construct($lang='en') 
    {
        parent::__construct(Mage::getResourceModel('directory'));
        
        $countryTable = $this->_dbModel->getTableName('directory', 'country');
        $countryNameTable = $this->_dbModel->getTableName('directory', 'country_name');
        
        $this->_sqlSelect->from($countryTable);
        $this->_sqlSelect->join($countryNameTable, "$countryNameTable.country_id=$countryTable.country_id AND $countryNameTable.language_code='$lang'");
        
        $this->setItemObjectClass('Mage_Directory_Country');
    }
    
    public function toHtmlOptions($default=false)
    {
        $out = '';
        foreach ($this->_items as $index => $item) {
            $out.='<option value="'.$item->countryId.'"';
            if ($default == $item->countryId) {
                $out.=' selected';
            }
            $out.='>' . $item->countryName;
            $out.="</option>\n";
        }
         
        return $out;
    }
}