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
 * Links block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Links extends Mage_Wishlist_Block_Links
{
    /**
     * Block position in menu
     *
     * @var int
     */
    protected $_position = 30;

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
     * Set custom wishlist for block
     * Used by external modules to substitute wishlist
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

    /**
     * Create Button label
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if (Mage::helper('enterprise_wishlist')->isMultipleEnabled()) {
            if ($count > 1) {
                return $this->__('My Wishlists (%d items)', $count);
            } else if ($count == 1) {
                return $this->__('My Wishlists (%d item)', $count);
            } else {
                return $this->__('My Wishlists');
            }
        } else {
            return parent::_createLabel($count);
        }
    }
}
