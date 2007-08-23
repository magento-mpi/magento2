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

class Mage_Catalog_Block_Product_List_Related extends Mage_Catalog_Block_Product_Abstract
{
	protected function _prepareData() 
	{
		Mage::registry('product')->getRelatedProducts()
			->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('thumbnail')
			->addAttributeToSort('position', 'asc')
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