<?php
/**
 * Wishlist Report collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Wishlist_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $wishlistTable;
    
    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
        
        $this->wishlistTable = Mage::getSingleton('core/resource')->getTableName('wishlist/wishlist');
    }
        
    public function getWishlistCustomerCount()
    {   
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->load();
        
        $customers = $collection->getSize();
        
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->getSelect()->from(array('wt' => $this->wishlistTable), array("cnt" => "count(wishlist_id)"))
                    ->where('wt.customer_id=e.entity_id')
                    ->group('wt.wishlist_id');
        $collection->load();
        $wishlists = $collection->getItems();
        $wishlists = array_shift($wishlists);
        return array(($wishlists->getCnt()*100)/$customers, $wishlists->getCnt());
    }
    
    public function getSharedCount()
    {   
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->load();
        
        $customers = $collection->getSize();
        
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->getSelect()->from(array('wt' => $this->wishlistTable), array("cnt" => "count(wishlist_id)"))
                    ->where('wt.customer_id=e.entity_id')
                    ->group('wt.wishlist_id');
        $collection->load();
        $wishlists = $collection->getItems();
        $wishlists = array_shift($wishlists);
        return array(($wishlists->getCnt()*100)/$customers, $wishlists->getCnt());
    }
    
}