<?php
/**
 * Catalog super product link model resource
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Super_Link extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('catalog/product_super_link','link_id');
	}
	
	public function loadByProduct($link, $productId, $parentId)
	{
		$read = $this->getConnection('read');

        $select = $read->select()->from($this->getMainTable())
            ->where('product_id=?', $productId)
            ->where('parent_id=?',	$parentId);
        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }

        $link->setData($data);

        $this->_afterLoad($link);
        return true;
	}
}// Class Mage_Catalog_Model_Entity_Product_Super_Link END