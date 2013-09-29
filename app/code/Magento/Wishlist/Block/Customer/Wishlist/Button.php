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
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_wishlistConfig = $wishlistConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve current wishlist
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist()
    {
        return $this->_wishlistData->getWishlist();
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
