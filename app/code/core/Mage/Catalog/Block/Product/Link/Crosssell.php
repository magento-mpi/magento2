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

class Mage_Catalog_Block_Product_Link_Crosssell extends Mage_Core_Block_Template
{
	protected function _prepareData() 
	{
		Mage::registry('product')->getCrossSellProducts()
			->addAttributeToSelect('name')
			->addAttributeToSelect('price')
			->addAttributeToSelect('small_image')
			->useProductItem()
			->load();
	}
	
	protected function	_beforeToHtml()
	{
		$this->_prepareData();
		return parent::_beforeToHtml();
	}
	
	public function getItems() {
		return Mage::registry('product')->getCrossSellProducts();
	}
}// Mage_Catalog_Block_Product_Link_Crosssell END