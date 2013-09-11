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
namespace Magento\MultipleWishlist\Block\Customer\Wishlist;

class Management extends \Magento\Core\Block\Template
{
    /**
     * Id of current customer
     *
     * @var int|null
     */
    protected $_customerId = null;

    /**
     * Wishlist Collection
     *
     * @var \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_current = null;

    /**
     * Render block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (\Mage::helper('Magento\MultipleWishlist\Helper\Data')->isMultipleEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Retrieve customer Id
     *
     * @return int|null
     */
    protected function _getCustomerId()
    {
        if (is_null($this->_customerId)) {
            $this->_customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
        }
        return $this->_customerId;
    }

    /**
     * Retrieve wishlist collection
     *
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getWishlists()
    {
        return \Mage::helper('Magento\MultipleWishlist\Helper\Data')->getCustomerWishlists($this->_getCustomerId());
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getDefaultWishlist()
    {
        return \Mage::helper('Magento\MultipleWishlist\Helper\Data')->getDefaultWishlist();
    }

    /**
     * Retrieve currently selected wishlist
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getCurrentWishlist()
    {
        if (!$this->_current) {
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_current = $this->getWishlists()->getItemById($wishlistId);
            } else {
                $this->_current = $this->getDefaultWishlist();
            }
        }
        return $this->_current;
    }

    /**
     * Build string that displays the number of items in wishlist
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @return string
     */
    public function getItemCount(\Magento\Wishlist\Model\Wishlist $wishlist)
    {
        $count = \Mage::helper('Magento\MultipleWishlist\Helper\Data')->getWishlistItemCount($wishlist);
        if ($count == 1) {
            return __('1 item');
        } else {
            return __('%1 items', $count);
        }
    }

    /**
     * Build wishlist management page url
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @return string
     */
    public function getWishlistManagementUrl(\Magento\Wishlist\Model\Wishlist $wishlist)
    {
        return $this->getUrl('wishlist/*/*', array('wishlist_id' => $wishlist->getId()));
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
     * Build wishlist edit url
     *
     * @param int $wishlistId
     * @return string
     */
    public function getEditUrl($wishlistId)
    {
        return $this->getUrl('wishlist/index/editwishlist', array('wishlist_id' => $wishlistId));
    }

    /**
     * Build wishlist items copy url
     *
     * @return string
     */
    public function getCopySelectedUrl()
    {
        return $this->getUrl('wishlist/index/copyitems', array('wishlist_id' => '%wishlist_id%'));
    }

    /**
     * Build wishlist items move url
     *
     * @return string
     */
    public function getMoveSelectedUrl()
    {
        return $this->getUrl('wishlist/index/moveitems', array('wishlist_id' => '%wishlist_id%'));
    }

    /**
     * Get wishlist item copy url
     *
     * @return string
     */
    public function getCopyItemUrl()
    {
        return $this->getUrl('wishlist/index/copyitem');
    }

    /**
     * Get wishlist item move url
     *
     * @return string
     */
    public function getMoveItemUrl()
    {
        return $this->getUrl('wishlist/index/moveitem');
    }

    /**
     * Check whether user multiple wishlist limit reached
     *
     * @param $wishlists
     * @return bool
     */
    public function canCreateWishlists($wishlists)
    {
        return !\Mage::helper('Magento\MultipleWishlist\Helper\Data')->isWishlistLimitReached($wishlists);
    }
}
