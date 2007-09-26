<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist product collection
 *
 * @category   Mage
 * @package    Mage_Wishlist
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

		$this->joinTable('wishlist/item', 'product_id=e_id', array(
		    'product_id' => 'product_id',
		    'description' => 'description',
		    'store_id' => 'store_id',
		    'added_at' => 'added_at',
		    'wishlist_id' => 'wishlist_id',
		), array('wishlist_id'=>$wishlist->getId()));
		/*
		$this->joinField('wishlist_item_id', 'wishlist/item', 'wishlist_item_id',  'product_id=e_id', array('wishlist_id'=>$wishlist->getId()))
			->joinField('product_id', 'wishlist/item', 'product_id',  'wishlist_item_id=wishlist_item_id')
			->joinField('description', 'wishlist/item' , 'description',  'wishlist_item_id=wishlist_item_id')
			->joinField('store_id', 'wishlist/item', 'store_id',  'wishlist_item_id=wishlist_item_id')
			->joinField('added_at', 'wishlist/item', 'added_at',  'wishlist_item_id=wishlist_item_id')
			->joinField('wishlist_id', 'wishlist/item', 'wishlist_id',  'wishlist_item_id=wishlist_item_id');
	   */
		return $this;
	}
}// Class Mage_Wishlist_Model_Mysql_Item_Collection END