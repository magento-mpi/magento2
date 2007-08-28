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
        
        $customers = $collection->count();
        
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->getSelect()->from(array('wt' => $this->wishlistTable))
                    ->where('wt.customer_id=e.entity_id')
                    ->group('wt.wishlist_id');
        $collection->load();
        $count = $collection->count();
        return array(($count*100)/$customers, $count);
    }
    
    public function getSharedCount()
    {          
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->getSelect()->from(array('wt' => $this->wishlistTable))
                    ->where('wt.customer_id=e.entity_id')
                    ->where('wt.shared=1')
                    ->group('wt.wishlist_id');
        $collection->load();
        return $collection->count();
    }
    
}