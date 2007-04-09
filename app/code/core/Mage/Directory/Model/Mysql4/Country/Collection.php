<?php
/**
 * Country collection
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Model_Mysql4_Country_Collection extends Varien_Data_Collection_Db
{
    public function __construct($useDomainConfig=true) 
    {
        parent::__construct(MMage::registry('resources')->getConnection('directory_read'));
        
        $countryTable = Mage::registry('resources')->getTableName('directory', 'country');
        $countryNameTable = Mage::registry('resources')->getTableName('directory', 'country_name');
        $lang = Mage::registry('website')->getLanguage();
        
        $this->_sqlSelect->from($countryTable);
        $this->_sqlSelect->join($countryNameTable, "$countryNameTable.country_id=$countryTable.country_id AND $countryNameTable.language_code='$lang'");
        
        if ($useDomainConfig) {
            $config = Mage::getConfig()->getCurrentDomain();
            // TODO
        }
        
        $this->setItemObjectClass(Mage::getConfig()->getResourceModelClassName('directory', 'country'));
    }
        
    public function toHtmlOptions($default=false)
    {
        $out = '';
        foreach ($this->_model->getItems() as $index => $item) {
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