<?php
/**
 * Country collection
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Country_Collection
{
    protected $_model = null;
    
    public function __construct($useDomainConfig=true) 
    {
        $this->_model = Mage::getResourceModel('directory', 'country_collection', array($useDomainConfig));
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