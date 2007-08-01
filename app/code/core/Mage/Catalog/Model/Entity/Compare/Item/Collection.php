<?php
/**
 * Catalog compare item collection model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Compare_Item_Collection extends Mage_Catalog_Model_Entity_Product_Collection
{
	protected $_customerId = 0;
	protected $_visitorId  = 0;
	
	public function __construct() 
    {
        $this->setEntity(Mage::getResourceSingleton('catalog/product'))
           	->setObject('catalog/compare_item');   
    }
	
	public function setCustomerId($customerId)
	{
		$this->_customerId = $customerId;
		$this->_addJoinToSelect();
		return $this;
	}
	
	public function setVisitorId($visitorId) 
	{
		$this->_visitorId = $visitorId;
		$this->_addJoinToSelect();
		return $this;
	}
	
	public function getCustomerId()
	{
		return $this->_customerId;
	}
	
	public function getVisitorId()
	{
		return $this->_visitorId;
	}
	
	public function getConditionForJoin()
	{
		if($this->getCustomerId()) {
			return array('customer_id'=>$this->getCustomerId());
		}
		
		if($this->getVisitorId()) {
			return array('visitor_id'=>$this->getVisitorId());
		}
		
		return null;
	}
	
	public function _addJoinToSelect()
	{
		$this->joinField('catalog_compare_item_id', 'catalog/compare_item','catalog_compare_item_id', 'product_id=entity_id', $this->getConditionForJoin());
		$this->joinField('product_id', 'catalog/compare_item','product_id', 'catalog_compare_item_id=catalog_compare_item_id');
		$this->joinField('customer_id', 'catalog/compare_item', 'customer_id', 'catalog_compare_item_id=catalog_compare_item_id');
		$this->joinField('visitor_id', 'catalog/compare_item', 'visitor_id', 'catalog_compare_item_id=catalog_compare_item_id');
		return $this;
	}
	
	public function useProductItem()
    {
    	$this->setObject('catalog/product');
    	return $this;
    }
    
    
}// Class Mage_Catalog_Model_Entity_Compare_Item_Collection END