<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist block customer item cart column
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Wishlist_Button extends Mage_Core_Block_Template
{
    /**
     * Retrieve current wishlist
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getWishlist()
    {
        return Mage::helper('Mage_Wishlist_Helper_Data')->getWishlist();
    }
}
