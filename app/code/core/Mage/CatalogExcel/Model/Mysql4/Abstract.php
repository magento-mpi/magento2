<?php

abstract class Mage_CatalogExcel_Model_Mysql4_Abstract
{
	protected $_skuAttribute;
	
	public function getConnection()
	{
		return Mage::getSingleton('core/resource')->getConnection('catalog_write');
	}
	
	public function getSelect()
	{
		return $this->getConnection()->select();
	}
	
	public function getTable($table)
	{
		return Mage::getSingleton('core/resource')->getTableName($table);
	}
	
	public function getSkuAttribute($field='attribute_id')
	{
		if (!$this->_skuAttribute) {
			$this->_skuAttribute = Mage::getModel('eav/entity_setup', 'eav_setup')->getAttribute('catalog_product', 'sku');
		}
		return isset($this->_skuAttribute[$field]) ? $this->_skuAttribute[$field] : null;
	}
}