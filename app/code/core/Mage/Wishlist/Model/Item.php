<?php
/**
 * Wishlist item model
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Item extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('wishlist/item');
	}
	
	public function getDataForSave()
	{
		$data = array();
		$data['product_id']  = $this->getProductId();
		$data['wishlist_id'] = $this->getWishlistId();
		$data['added_at'] 	 = $this->getAddedAt() ? $this->getAddedAt() : now();
		$data['description'] = $this->getDescription();
		$data['store_id']	 = $this->getStoreId() ? $this->getStoreId() : Mage::getSingleton('core/store')->getId();
		
		return $data;
	}
	
	public function loadByProductWishlist($wishlistId, $productId, $sharedStores) 
	{
		$this->getResource()->loadByProductWishlist($this, $wishlistId, $productId, $sharedStores);
		return $this;
	}	
}// Class Mage_Wishlist_Model_Item END