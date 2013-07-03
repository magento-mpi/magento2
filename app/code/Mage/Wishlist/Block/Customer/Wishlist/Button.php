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
     * Wishlist config
     *
     * @var Mage_Wishlist_Model_Config
     */
    protected $_wishlistConfig;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Wishlist_Model_Config $wishlistConfig
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context, Mage_Wishlist_Model_Config $wishlistConfig, array $data = array()
    ) {
        $this->_wishlistConfig = $wishlistConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current wishlist
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getWishlist()
    {
        return Mage::helper('Mage_Wishlist_Helper_Data')->getWishlist();
    }

    /**
     * Retrieve wishlist config
     *
     * @return Mage_Wishlist_Model_Config
     */
    public function getConfig()
    {
        return $this->_wishlistConfig;
    }
}
