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
	protected $_isLoaded  = false;
	
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
	
	public function getIsLoaded()
	{
		return $this->_isLoaded;
	}
	
	public function load($printQuery=false, $logQuery=false)
	{
		$oldStoreId = $this->getEntity()->getStoreId();
		if(!isset($this->_joinFields['store_id'])) {
			$this->getEntity()->setStore(0);
		}
		$this->_isLoaded = true;
		parent::load($printQuery, $logQuery);
		if(!isset($this->_joinFields['store_id'])) {
			$this->getEntity()->setStore($oldStoreId);
		}
		return $this;
	}
	
	public function useProductItem()
    {
    	$this->setObject('catalog/product');
    	return $this;
    }
    
    public function setProductFilter($product)
    {
    	$this->addFieldToFilter('parent_id', (int) $product->getId());
    	return $this;
    }
    
    
    
}// Class Mage_Catalog_Model_Entity_Product_Super_Link_Collection END