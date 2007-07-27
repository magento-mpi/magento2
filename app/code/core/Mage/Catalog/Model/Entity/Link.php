<?php
/**
 * Catalog product link resource model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Link extends Mage_Core_Model_Mysql4_Abstract
{
	protected function  _construct() 
	{
		$this->_init('catalog/product_link', 'link_id');
	}
	
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		foreach(array_unique($object->getAttributesCollection()->getColumnValues('data_type')) as $table) {
			// Loading of link attributes from unique data tables.
			$attributeFirst = $object->getAttributesCollection()->getItemByColumnValue('data_type', $table);
			$select = $this->getConnection('read')->select()
				->from($attributeFirst->getTypeTable())
				->where('link_id = ?', $object->getId());
			
			$attributesValues = $this->getConnection('read')->fetchAll($select);
			foreach ($attributesValues as $attributeValue) {
				$attribute = $object->getAttributesCollection()->getItemById($attributeValue['product_link_attribute_id']);
				if($attribute) {
					$object->setData($attribute->getCode(), $attributeValue['value']);
				}
			}
		}
		
		return $this;
	}
	
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$originAttributes = array();
		
		foreach (array_unique($object->getAttributesCollection()->getColumnValues('data_type')) as $table) {
			// Loading of link attributes ids from unique data tables.
			$attributeFirst = $object->getAttributesCollection()->getItemByColumnValue('data_type', $table);
			$select = $this->getConnection('read')->select()
				->from($attributeFirst->getTypeTable(), array('value_id', 'product_link_attribute_id'))
				->where('link_id = ?', $object->getId());
			
			$attributesValues = $this->getConnection('read')->fetchAll($select);
			
			foreach ($attributesValues as $attributeValue) {
				$attribute = $object->getAttributesCollection()->getItemById($attributeValue['product_link_attribute_id']);
				
				if($attribute) {
					$originAttributes[$attribute->getId()] = $attributeValue;
				}
			}
		}
		
		$this->getConnection('write')->beginTransaction();
		try {
			
			foreach ($object->getAttributesCollection() as $attribute)
			{
				if(isset($originAttributes[$attribute->getId()])) {
					// If attribute value exists update existing record
					$data = array();
					$data['value'] = $object->getData($attribute->getCode());
					$condition = $this->getConnection('write')->quoteInto('value_id = ?', $originAttributes[$attribute->getId()]['value_id']);				
					$this->getConnection('write')->update($attribute->getTypeTable(), $data, $condition);
				} else if($object->getData($attribute->getCode()) !== null) {
					// If attribute value not empty and not exists insert new record
					$data = array();
					$data['value'] = $object->getData($attribute->getCode());
					$data['product_link_attribute_id'] = $attribute->getId();
					$data['link_id'] = $object->getId();
					$this->getConnection('write')->insert($attribute->getTypeTable());
				}
			}
			
			$this->getConnection('write')->commit();
		}
		catch (Exception $e) {
			$this->getConnection('write')->rollBack();
			throw $e;
		}
		
		return $this;
	}
	
}// Class Mage_Catalog_Model_Entity_Product_Link END