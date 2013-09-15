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
 * Behaviour block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block;

class Behaviour extends \Magento\Core\Block\Template
{
    /**
     * Wishlist data
     *
     * @var Magento_MultipleWishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_MultipleWishlist_Helper_Data $wishlistData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_MultipleWishlist_Helper_Data $wishlistData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve wishlists items
     *
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getWishlists()
    {
        return $this->_wishlistData->getCustomerWishlists();
    }

    /**
     * Retrieve add item to wishlist url
     *
     * @return string
     */
    public function getAddItemUrl()
    {
        return $this->getUrl('wishlist/index/add', array('wishlist_id' => '%wishlist_id%'));
    }

    /**
     * Retrieve Wishlist creation url
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('wishlist/index/createwishlist');
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getDefaultWishlist()
    {
        return $this->_wishlistData->getDefaultWishlist();
    }

    /**
     * Check whether customer reached wishlist limit
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection
     * @return bool
     */
    public function canCreateWishlists($wishlistList)
    {
        $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
        return !$this->_wishlistData->isWishlistLimitReached($wishlistList) && $customerId;
    }

    /**
     * Get customer wishlist list
     *
     * @return array
     */
    public function getWishlistShortList()
    {
        $wishlistData = array();
        foreach($this->getWishlists() as $wishlist){
            $wishlistData[] = array('id' => $wishlist->getId(), 'name' => $wishlist->getName());
        }
        return $wishlistData;
    }

    /**
     * Render block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_wishlistData->isMultipleEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }
}
