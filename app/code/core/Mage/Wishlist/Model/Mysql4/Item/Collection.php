<?php
/**
 * Wishlist item collection
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Mysql4_Item_Collection extends Mage_Catalog_Model_Entity_Product_Collection 
{
	protected $_resource = null;
	
	public function __construct() 
	{
		$this->setEntity(Mage::getResourceSingleton('catalog/product'));
        $this->setObject('wishlist/item');
	}
	
	public function addWishlistFilter(Mage_Wishlist_Model_Wishlist	$wishlist)
	{
		$this->getSelect()
			->join(array('wishlist_item'=>$this->getTable('wishlist/item')), 'e.entity_id = wishlist_item.product_id', array('*',new Zend_Db_Expr("(TO_DAYS('" . now() . "') - TO_DAYS(wishlist_item.added_at)) as days_in_wishlist")))
			->where('wishlist_item.wishlist_id = ?', $wishlist->getId());
		
		return $this;
	}
	
	
	public function addWebsiteData()
	{
		$this->getSelect()
			->join(array('store'=>$this->getTable('core/store')), 'store.store_id = wishlist_item.store_id', array())
			->join(array('website'=>$this->getTable('core/website')), 'website.website_id = store.website_id', 'website_id');
		
		return $this;
	}
	
	    
    public function getTable($table)
    {
        return Mage::getSingleton('core/resource')->getTableName($table);
    }
}// Class Mage_Wishlist_Model_Mysql_Item_Collection END