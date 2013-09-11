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
namespace Magento\Wishlist\Block\Customer\Wishlist;

class Button extends \Magento\Core\Block\Template
{
    /**
     * Wishlist config
     *
     * @var \Magento\Wishlist\Model\Config
     */
    protected $_wishlistConfig;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context, \Magento\Wishlist\Model\Config $wishlistConfig, array $data = array()
    ) {
        $this->_wishlistConfig = $wishlistConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current wishlist
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist()
    {
        return \Mage::helper('Magento\Wishlist\Helper\Data')->getWishlist();
    }

    /**
     * Retrieve wishlist config
     *
     * @return \Magento\Wishlist\Model\Config
     */
    public function getConfig()
    {
        return $this->_wishlistConfig;
    }
}
