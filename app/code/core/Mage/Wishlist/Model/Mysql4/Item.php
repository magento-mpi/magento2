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
	protected function _construct()
	{
		$this->_init('wishlist/item', 'wishlist_item_id');
	}
	
	public function getDataForSave()
	{
		$data = array();
		$data['product_id']  = $this->getProductId();
		$data['wishlist_id'] = $this->getWishlistId();
		$data['added_at'] 	 = $this->getAddedAt() ? $this->getAddedAt() : now();
		$data['description'] = $this->getDescription();
		
		return $data;
	}
}// Class Mage_Wishlist_Model_Mysql4_Item END