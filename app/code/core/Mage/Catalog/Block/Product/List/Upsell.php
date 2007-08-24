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

class Mage_Catalog_Block_Product_List_Upsell extends Mage_Catalog_Block_Product_Abstract
{
    protected $_columnCount = 4;
    protected $_items;
    
	protected function _prepareData() 
	{
		Mage::registry('product')->getUpSellProducts()
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
	
	public function getItemCollection()
	{
	    return Mage::registry('product')->getUpSellProducts();
	}
	
	public function getItems() {
	    if (is_null($this->_items)) {
	        $this->_items = $this->getItemCollection()->getItems();
	    }
		return $this->_items;
	}
	
	public function getRowCount()
	{
	    return ceil($this->getItemCollection()->getSize()/$this->getColumnCount());
	}
	
	public function getColumnCount()
	{
	    return $this->_columnCount;
	}
	
	public function resetItemsIterator()
	{
	    $this->getItems();
	    reset($this->_items);
	}
	
	public function getIterableItem()
	{
	    $item = current($this->_items);
	    next($this->_items);
	    return $item;
	}
}// Mage_Catalog_Block_Product_Link_Upsell END