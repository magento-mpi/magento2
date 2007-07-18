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

class Mage_Wishlist_Model_Mysql_Item_Collection extends Mage_Catalog_Model_Entity_Product_Collection 
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
			->join(array('wishlist_item'=>$this->getTable('item')), 'e.entity_id = wishlist_item.product_id')
			->where('wishlist_item.wishlist_id = ?', $wishlist->getId());
	}
	
	/**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('wishlist/item');
        }
        
        return $this->_resource;
    }
    
    public function getTable($table)
    {
        return $this->getResource()->getTable($table);
    }
}// Class Mage_Wishlist_Model_Mysql_Item_Collection END