<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Behaviour block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block;

class Behaviour extends \Magento\Framework\View\Element\Template
{
    /**
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data|null
     */
    protected $_wishlistData = null;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * {@inheritdoc}
     *
     * @var bool
     */
    protected $_isScopePrivate = true;

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
        array $data = []
    ) {
        $this->_wishlistData = $wishlistData;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
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
        return $this->getUrl('wishlist/index/add', ['wishlist_id' => '%wishlist_id%']);
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
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlistList
     * @return bool
     */
    public function canCreateWishlists($wishlistList)
    {
        $customerId = $this->currentCustomer->getCustomerId();
        return !$this->_wishlistData->isWishlistLimitReached($wishlistList) && $customerId;
    }

    /**
     * Get customer wishlist list
     *
     * @return array
     */
    public function getWishlistShortList()
    {
        $wishlistData = [];
        foreach ($this->getWishlists() as $wishlist) {
            $wishlistData[] = ['id' => $wishlist->getId(), 'name' => $wishlist->getName()];
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
