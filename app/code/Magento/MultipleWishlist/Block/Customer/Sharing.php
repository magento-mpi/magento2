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
 * Wishlist sidebar block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Customer_Sharing extends Magento_Wishlist_Block_Customer_Sharing
{
    /**
     * Retrieve send form action URL
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('*/*/send', array('wishlist_id' => Mage::helper('Magento_Wishlist_Helper_Data')->getWishlist()->getId()));
    }

    /**
     * Retrieve back button url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', array('wishlist_id' => Mage::helper('Magento_Wishlist_Helper_Data')->getWishlist()->getId()));
    }
}
