<?php
/**
 * Catalog super product link collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Super_Link_Collection extends Mage_Catalog_Model_Entity_Product_Collection
{
	public function __construct()
	{
		$this->setEntity(Mage::getResourceSingleton('catalog/product'))
           	->setObject('catalog/product_super_link');
	}
	
	protected function _joinLink()
	{
		$this->joinField('link_id', 'catalog/product_super_link', 'link_id', 'product_id=entity_id')
			->joinField('parent_id', 'catalog/product_super_link', 'parent_id', 'link_id=link_id')
			->joinField('product_id', 'catalog/product_super_link', 'product_id', 'link_id=link_id');
		
		return $this;
	}
	
	public function resetSelect()
	{
		$result = parent::resetSelect();
		$this->_joinLink();
		return $result;
	}
	
	public function useProductItem()
    {
    	$this->setObject('catalog/product');
    	return $this;
    }
    
}// Class Mage_Catalog_Model_Entity_Product_Super_Link_Collection END