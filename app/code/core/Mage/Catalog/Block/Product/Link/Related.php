<?php
/**
 * Catalog product related items block
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Block_Product_Link_Related extends Mage_Core_Block_Template
{
	protected function _prepareData() 
	{
		Mage::registry('product')->getRelatedProducts()
			->addAttributeToSelect('name')
			->addAttributeToSelect('price')
			->addAttributeToSelect('small_image')
			->joinField('store_id', 
	                'catalog/product_store', 
	                'store_id', 
	                'product_id=entity_id', 
	                array('store_id'=>Mage::getSingleton('core/store')->getId()))
			->useProductItem()
			->load();
	}
	
	protected function	_beforeToHtml()
	{
		$this->_prepareData();
		return parent::_beforeToHtml();
	}
	
	public function getItems() {
		return Mage::registry('product')->getRelatedProducts();
	}
}// Class Mage_Catalog_Block_Product_Link_Related END