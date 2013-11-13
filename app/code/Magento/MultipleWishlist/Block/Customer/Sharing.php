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
namespace Magento\MultipleWishlist\Block\Customer;

class Sharing extends \Magento\Wishlist\Block\Customer\Sharing
{
    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Core\Model\Session\Generic $wishlistSession
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Core\Model\Session\Generic $wishlistSession,
        \Magento\Wishlist\Helper\Data $wishlistData,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($context, $coreData, $wishlistConfig, $wishlistSession, $data);
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
