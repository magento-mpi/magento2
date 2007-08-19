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
    protected function _construct() 
    {
        $this->_init('directory/country');
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
    
    public function formatAddress(Varien_Object $address, $html=false)
    {
    	$address->getRegion();
    	$address->getCountry();
    	
    	$template = $this->getData('address_template_'.($html ? 'html' : 'plain'));
    	if (empty($template)) {
    		if (!$html) {
    			$template = "{{firstname}} {{lastname}}
{{company}}
{{street1}}
{{street2}}
{{city}}, {{region}} {{postcode}}";
    		} else {
    			$template = "<b>{{firstname}} {{lastname}}</b><br/>
{{street}}<br/>
{{city}}, {{region}} {{postcode}}<br/>
T: {{telephone}}";
    		}
    	}
    	
    	$filter = new Varien_Filter_Template_Simple();
    	$addressText = $filter->setData($address->getData())->filter($template);
    	
    	if ($html) {
    		$addressText = preg_replace('#(<br\s*/?>\s*){2,}#im', '<br/>', $addressText);
    	} else {
    		$addressText = preg_replace('#(\n\s*){2,}#m', "\n", $addressText);
    	}
    	
    	return $addressText;
    }
}