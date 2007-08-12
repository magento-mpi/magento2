<?php
/**
 * Catalog super product attribute pricing collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Super_Attribute_Pricing_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('catalog/product_super_attribute_pricing');
	}
	
	protected function addLinksFilter(array $links) 
	{
		$condition = array();
		foreach ($links as $link) {
			foreach ($link as $attribute) {
				$condition[] = '(' . $this->getConnection()->quoteInto('attribute_id = ?', $attribute['attribute_id']) 
							 . ' AND ' . $this->getConnection()->quoteInto('value_index = ?', $attribute['value_index']) . ')';
			}
		}
		if(sizeof($condition)==0) {
			$condition[] = '0';
		}
		
		$this->getSelect()->where(new Zend_Db_Expr('(' . join(' OR ', $condition) . ')'));
	}
}// Class Mage_Catalog_Model_Entity_Product_Super_Attribute_Pricing_Collection END