<?php
/**
 * Catalog super product attribute resource model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Super_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('catalog/product_super_attribute', 'product_super_attribute_id');
	}
	
	protected  function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		$select = $this->getConnection('read')->select()
			->from($this->getTable('product_super_attribute_label'))
			->where($this->getIdFieldName() . ' = ? ', $object->getId())
			->where('store_id = ?', (int) $this->getStoreId());

		$data = $read->fetchRow($select);
	   	if (!$data) {
            return $this;
        }
		
        $object->setStoreId($data['store_id'])
        	->setLabel($data['value']);
        	
		return $this;
	}
	
	protected function _afterSave(Mage_Core_Model_Abstract $object) 
	{
		$select =  $this->getConnection('read')->select()
			->from($this->getTable('product_super_attribute_label'), 'value_id')
			->where($this->getIdFieldName() . ' = ? ', $object->getId())
			->where('store_id = ?', (int) $object->getStoreId());

		$valueId = $this->getConnection('read')->fetchOne($select);
		
		$data = array();
		$data['store_id'] 			   = $object->getStoreId();
		$data[$this->getIdFieldName()] = $object->getId();
		$data['value']	  			   = $object->getLabel();
		
		if($valueId) {
			$this->getConnection('write')->update($this->getTable('product_super_attribute_label'), 
				$data,
				'value_id = '.(int) $valueId);
		} else {
			$this->getConnection('write')
				->insert($this->getTable('product_super_attribute_label'), $data);
		}
		
		$valuePricing = $object->getValues();

		if(!is_array($valuePricing)) {
			$valuePricing = array();
		}
		
		$ignoreDeleteIds = array();
		
		foreach ($valuePricing as $value) {
			$pricing = Mage::getModel('catalog/product_super_attribute_pricing')
				->setData($value)
				->setId(isset($value['id']) ? $value['id'] : null)
				->setData($this->getIdFieldName(), $object->getId())
				->save();
			
			$ignoreDeleteIds[] = $pricing->getId();
		}
		
		$deleteCondition = $this->getConnection('write')->quoteInto($this->getIdFieldName().' = ?', 
																	$object->getId());
		
		if(sizeof($ignoreDeleteIds)>0) {
			$deleteCondition.= ' AND '.$this->getConnection('write')->quoteInto('value_id NOT IN(?)', $ignoreDeleteIds);
		}
		
		$this->getConnection('write')
			->delete($this->getTable('product_super_attribute_pricing'), $deleteCondition);
			
		return $this;
	}
	
	public function getPricingCollection($superAttribute)
	{
		$collection = Mage::getResourceModel('catalog/product_super_attribute_pricing_collection')
			->addFieldToFilter($this->getIdFieldName(), $superAttribute->getId());
		
		return $collection;
	}
	
}// Class Mage_Catalog_Model_Entity_Product_Super_Attribute`END