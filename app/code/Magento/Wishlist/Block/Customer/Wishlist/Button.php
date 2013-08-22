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
 * Wishlist block customer item cart column
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Block_Customer_Wishlist_Button extends Magento_Core_Block_Template
{
    /**
     * Wishlist config
     *
     * @var Magento_Wishlist_Model_Config
     */
    protected $_wishlistConfig;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Wishlist_Model_Config $wishlistConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context, Magento_Wishlist_Model_Config $wishlistConfig, array $data = array()
    ) {
        $this->_wishlistConfig = $wishlistConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current wishlist
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getWishlist()
    {
        return Mage::helper('Magento_Wishlist_Helper_Data')->getWishlist();
    }

    /**
     * Retrieve wishlist config
     *
     * @return Magento_Wishlist_Model_Config
     */
    public function getConfig()
    {
        return $this->_wishlistConfig;
    }
}
