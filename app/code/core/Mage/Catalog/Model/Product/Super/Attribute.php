<?php
/**
 * Catalog super product attribute model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Product_Super_Attribute extends Mage_Core_Model_Abstract
{
	protected $_pricingCollection = null;
	
	protected function _construct()
	{
		$this->_init('catalog/product_super_attribute');
	}
	
	public function getDataForSave()
	{
		return $this->toArray(array('product_id','attribute_id','position'));
	}
	
	public function getPricingCollection() 
	{
		if(is_null($this->_pricingCollection)) {
			$this->_pricingCollection = $this->getResource()->getPricingCollection($this);
		}
		
		return $this->_pricingCollection;
	}
	
	public function getValues(Mage_Eav_Model_Entity_Attribute_Abstract $attribute=null)
	{
		if($this->getData('values')) {
			return $this->getData('values');
		}
		
		if(!is_null($attribute)) {
			$this->getPricingCollection()->walk('setPricingLabelFromAttribute', array($attribute));
		}
		$collectionToArray = $this->getPricingCollection()->toArray(array('value_id', 'value_index', 'label', 'is_percent', 'pricing_value'));
		return $collectionToArray['items'];
	}
}// Class Mage_Catalog_Model_Product_Super_Attribute END