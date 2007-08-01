<?php
/**
 * Catalog compare item resource model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Compare_Item extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('catalog/compare_item', 'catalog_compare_item_id');
	}
	
	public function loadByProduct(Mage_Core_Model_Abstract $object, Mage_Catalog_Model_Product $product)
	{
		$read = $this->getConnection('read');

        $select = $read->select()->from($this->getMainTable())
            ->where('product_id=?',  $product->getID())
            ->where('customer_id=?', $object->getCustomerId())
            ->where('visitor_id=?',  $object->getVisitorId());
                
        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);
        return true;
	}
}// Class Mage_Catalog_Model_Entity_Compare_Item END