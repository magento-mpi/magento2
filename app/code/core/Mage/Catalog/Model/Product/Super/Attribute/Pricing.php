<?php
/**
 * Catalog super product attribute pricing model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Product_Super_Attribute_Pricing extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('catalog/product_super_attribute_pricing');
	}
	
	public function getDataForSave()
	{
		return $this->toArray(array('product_super_attribute_id','value_index','is_percent','pricing_value'));
	}
	
	public function setPricingLabelFromAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $attribute) 
	{
		if($attribute->getSourceModel()) {
			$this->setLabel($attribute->getSource()->getOptionText($this->getValueIndex()));
		} else {
			$this->setLabel($this->getValueIndex());
		}
		return $this;
	}
}// Class Mage_Catalog_Model_Product_Super_Attribute_Pricing END