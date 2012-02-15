<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Wishlist sidebar block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Customer_Sidebar extends Mage_Wishlist_Block_Customer_Sidebar
{
    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        if (Mage::helper('enterprise_wishlist')->isMultipleEnabled()) {
            return $this->__('My Wishlists <small>(%d)</small>', $this->getItemCount());
        } else {
            return parent::getTitle();
        }
    }

    /**
     * Create Wishlist Item Collection
     *
     * @param int $customerId
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createCollection($customerId)
    {
        $collection = Mage::getResourceModel('wishlist/item_collection')
            ->addCustomerIdFilter($customerId)
            ->addStoreFilter(Mage::app()->getStore()->getWebsite()->getStoreIds())
            ->setVisibilityFilter();
        return $collection;
    }

    /**
     * Set wishlist for block.
     * Used by persistent wishlist module
     *
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     */
    public function setCustomWishlist(Mage_Wishlist_Model_Wishlist $wishlist)
    {
        if (Mage::helper('enterprise_wishlist')->isMultipleEnabled()) {
            $collection = $this->_createCollection($wishlist->getCustomerId());
            Mage::helper('wishlist')->setWishlistItemCollection($collection);
        } else {
            parent::setCustomWishlist($wishlist);
        }
    }
}
