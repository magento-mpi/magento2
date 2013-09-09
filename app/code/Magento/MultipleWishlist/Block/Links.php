<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Links block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Links extends Magento_Wishlist_Block_Links
{
    /**
     * Count items in wishlist
     *
     * @return int
     */
    protected function _getItemCount()
    {
        return $this->helper('Magento_MultipleWishlist_Helper_Data')->getItemCount();
    }

    /**
     * Create Button label
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if (Mage::helper('Magento_MultipleWishlist_Helper_Data')->isMultipleEnabled()) {
            if ($count > 1) {
                return __('My Wish Lists (%1 items)', $count);
            } else if ($count == 1) {
                return __('My Wish Lists (%1 item)', $count);
            } else {
                return __('My Wish Lists');
            }
        } else {
            return parent::_createLabel($count);
        }
    }
}
