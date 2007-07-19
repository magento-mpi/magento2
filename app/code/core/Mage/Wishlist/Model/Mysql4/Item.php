<?php
/**
 * Wishlist item model resource
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract
{
	protected $_productIdFieldName = 'product_id';
	
	protected function _construct()
	{
		$this->_init('wishlist/item', 'wishlist_item_id');
	}
			
	public function loadByProductWishlist(Mage_Wishlist_Model_Item $item, $wishlistId, $productId)
	{
		$select = $this->getConnection('read')->select()
			->from(array('main_table'=>$this->getTable('item')))
			->where('main_table.wishlist_id = ?',  $wishlistId)
			->where('main_table.product_id = ?',  $productId);
			
		if($_data = $this->getConnection('read')->fetchRow($select)) {
			$item->setData($_data);
		}
		
		return $item;
	}
}// Class Mage_Wishlist_Model_Mysql4_Item END