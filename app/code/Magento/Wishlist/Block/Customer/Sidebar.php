<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist sidebar block
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Block_Customer_Sidebar extends Magento_Wishlist_Block_Abstract
{
    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        return __('My Wish List <small>(%1)</small>', $this->getItemCount());
    }

    /**
     * Add sidebar conditions to collection
     *
     * @param  Magento_Wishlist_Model_Resource_Item_Collection $collection
     * @return Magento_Wishlist_Block_Customer_Wishlist
     */
    protected function _prepareCollection($collection)
    {
        $collection->setCurPage(1)
            ->setPageSize(3)
            ->setInStockFilter(true)
            ->setOrder('added_at');

        return $this;
    }

    /**
     * Prepare before to html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getItemCount()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Can Display wishlist
     *
     * @deprecated after 1.6.2.0
     * @return bool
     */
    public function getCanDisplayWishlist()
    {
        return $this->_getCustomerSession()->isLoggedIn();
    }

    /**
     * Retrieve Wishlist Product Items collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    public function getWishlistItems()
    {
        if (is_null($this->_collection)) {
            $this->_collection = clone $this->_createWishlistItemCollection();
            $this->_collection->clear();
            $this->_prepareCollection($this->_collection);
        }

        return $this->_collection;
    }

    /**
     * Return wishlist items count
     *
     * @return int
     */
    public function getItemCount()
    {
        return $this->_getHelper()->getItemCount();
    }

    /**
     * Check whether user has items in his wishlist
     *
     * @return bool
     */
    public function hasWishlistItems()
    {
        return $this->getItemCount() > 0;
    }
}
