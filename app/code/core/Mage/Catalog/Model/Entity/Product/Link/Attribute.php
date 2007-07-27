<?php
/**
 * Catalog product link attribute resource model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Link_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('catalog/product_link_attribute', 'product_link_attribute_id');
	}
	
	public function getTypeTable(Mage_Catalog_Model_Product_Link_Attribute $attribute)
	{
		return $this->getTable($this->_mainTable . "_" . $attribute->getDataType());
	}
}// Class Mage_Catalog_Model_Entity_Product_Link_Attribute END