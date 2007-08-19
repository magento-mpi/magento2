<?php
/**
 * Wishlist product collection
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Entity_Product_Collection 
{
	public function addWishlistFilter(Mage_Wishlist_Model_Wishlist	$wishlist)
	{
		// Workaround for entity_id. 
		$this->_joinFields['e_id'] = array('table'=>'e','field'=>'entity_id');
		
		/**
		 * @todo need simple join !!!
		 */
		
		$this->joinField('wishlist_item_id', 'wishlist/item', 'wishlist_item_id',  'product_id=e_id', array('wishlist_id'=>$wishlist->getId()))
			->joinField('product_id', 'wishlist/item', 'product_id',  'wishlist_item_id=wishlist_item_id')
			->joinField('description', 'wishlist/item' , 'description',  'wishlist_item_id=wishlist_item_id')
			->joinField('store_id', 'wishlist/item', 'store_id',  'wishlist_item_id=wishlist_item_id')
			->joinField('added_at', 'wishlist/item', 'added_at',  'wishlist_item_id=wishlist_item_id')
			->joinField('wishlist_id', 'wishlist/item', 'wishlist_id',  'wishlist_item_id=wishlist_item_id');
		return $this;
	}
}// Class Mage_Wishlist_Model_Mysql_Item_Collection END