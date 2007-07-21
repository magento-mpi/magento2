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
		
		/*$this->_joinFields['e_id'] = array('table'=>'e','field'=>'entity_id');
		
		$this->joinField('description', 'wishlist/item' , 'description',  'product_id=e_id', array('wishlist_id'=>$wishlist->getId()))
			->joinField('store_id', 'wishlist/item', 'store_id',  'product_id=e_id');
		*/
				
		return $this;
	}
	
	
	public function addWebsiteData()
	{
		/*$this->joinField('store_name', 'core/store', 'name', 'store_id=store_id')
			->joinField('store_website_id', 'core/store', 'website_id', 'store_id=store_id')
			->joinField('website_name', 'core/website', 'name', 'website_id=store_website_id');
		*/
		return $this;
	}
	
	    
    public function getTable($table)
    {
        return Mage::getSingleton('core/resource')->getTableName($table);
    }
}// Class Mage_Wishlist_Model_Mysql_Item_Collection END