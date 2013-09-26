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
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Wishlist_Model_Config $wishlistConfig
     * @param Magento_Core_Model_Session_Generic $wishlistlSession
     * @param array $data
     */
    public function __construct(
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Wishlist_Model_Config $wishlistConfig,
        Magento_Core_Model_Session_Generic $wishlistlSession,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($coreData, $context, $wishlistConfig, $wishlistlSession, $data);
    }

    /**
     * Retrieve send form action URL
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('*/*/send', array('wishlist_id' => $this->_wishlistData->getWishlist()->getId()));
    }

    /**
     * Retrieve back button url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', array('wishlist_id' => $this->_wishlistData->getWishlist()->getId()));
    }
}
