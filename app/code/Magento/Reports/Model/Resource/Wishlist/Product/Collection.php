<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Wishlist_Product_Collection extends Magento_Wishlist_Model_Resource_Item_Collection
{
    /**
     * Resource initialization
     *
     */
    public function _construct()
    {
        $this->_init('Magento_Wishlist_Model_Wishlist', 'Magento_Wishlist_Model_Resource_Wishlist');
    }

    /**
     * Add wishlist count
     *
     * @return Magento_Reports_Model_Resource_Wishlist_Product_Collection
     */
    public function addWishlistCount()
    {
        $wishlistItemTable = $this->getTable('wishlist_item');
        $this->getSelect()
            ->join(
                array('wi' => $wishlistItemTable),
                'wi.product_id = e.entity_id',
                array('wishlists' => new Zend_Db_Expr('COUNT(wi.wishlist_item_id)')))
            ->where('wi.product_id = e.entity_id')
            ->group('wi.product_id');

        $this->getEntity()->setStore(0);
        return $this;
    }

    /**
     * add customer count to result
     *
     * @return Magento_Reports_Model_Resource_Wishlist_Product_Collection
     */
    public function getCustomerCount()
    {
        $this->getSelect()->reset();

        $this->getSelect()
            ->from(
                array('wishlist' => $this->getTable('wishlist')),
                array(
                    'wishlist_cnt' => new Zend_Db_Expr('COUNT(wishlist.wishlist_id)'),
                    'wishlist.customer_id'
                ))
            ->group('wishlist.customer_id');
        return $this;
    }

    /**
     * Get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns("COUNT(*)");

        return $countSelect;
    }

    /**
     * Set order to result
     *
     * @param string $attribute
     * @param string $dir
     * @return Magento_Reports_Model_Resource_Wishlist_Product_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if ($attribute == 'wishlists') {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }
}

