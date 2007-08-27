<?php
/**
 * Wishlist Report collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Wishlist_Product_Collection extends Mage_Wishlist_Model_Mysql4_Product_Collection
{
    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
    }
    
    public function addWishlistCount()
    {
        $wishlistItemTable = Mage::getSingleton('core/resource')->getTableName('wishlist/item');
              
        $this->getSelect()
            ->from(array('wi' => $wishlistItemTable), 'count(wishlist_item_id) as wishlists')
            ->where('wi.product_id=e.entity_id')
            ->group('wi.product_id');
        
        $this->getEntity()->setStore(0);
        return $this;
    }
    
    public function getCustomerCount()
    {
        $this->getSelect()->reset();
        $this->getSelect()->from("wishlist", array("count(wishlist_id) as wishlist_cnt"))
                    ->group("wishlist.customer_id");
        return $this;//->getItems()->;
    }
           
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::GROUP);

        $sql = $countSelect->__toString();
        
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(*) from ', $sql);

        return $sql;
    }
    
    public function setOrder($attribute, $dir='desc')
    {
        switch ($attribute)
        {
		case 'wishlists':
			$this->getSelect()->order($attribute . ' ' . $dir);	
			break;
		default:
			parent::setOrder($attribute, $dir);	
        }
        
        return $this;
    }
    
}
