<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist sidebar block
 */
namespace Magento\MultipleWishlist\Block\Customer\Wishlist;

use Magento\Wishlist\Model\Resource\Wishlist\Collection;

class Management extends \Magento\Framework\View\Element\Template
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
     * @var Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_current = null;

    /**
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\MultipleWishlist\Helper\Data $wishlistData,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Render block
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

    /**
     * Retrieve customer Id
     *
     * @return int|null
     */
    protected function _getCustomerId()
    {
        if (is_null($this->_customerId)) {
            $this->_customerId = $this->currentCustomer->getCustomerId();
        }
        return $this->_customerId;
    }

    /**
     * Retrieve wishlist collection
     *
     * @return Collection
     */
    public function getWishlists()
    {
        return $this->_wishlistData->getCustomerWishlists($this->_getCustomerId());
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
        $count = $this->_wishlistData->getWishlistItemCount($wishlist);
        if ($count == 1) {
            return __('1 item in wish list');
        } else {
            return __('%1 items in wish list', $count);
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
     * @param Collection $wishlists
     * @return bool
     */
    public function canCreateWishlists($wishlists)
    {
        return !$this->_wishlistData->isWishlistLimitReached($wishlists);
    }
}
