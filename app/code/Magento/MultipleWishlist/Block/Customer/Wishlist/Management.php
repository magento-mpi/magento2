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
 */
class Magento_MultipleWishlist_Block_Customer_Wishlist_Management extends Magento_Core_Block_Template
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
     * @var Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    protected $_collection;

    /**
     * @var Magento_Wishlist_Model_Wishlist
     */
    protected $_current = null;

    /**
     * Wishlist data
     *
     * @var Magento_MultipleWishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_MultipleWishlist_Helper_Data $wishlistData
     * @param Magento_Customer_Model_Session $customerSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_MultipleWishlist_Helper_Data $wishlistData,
        Magento_Customer_Model_Session $customerSession,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
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
            $this->_customerId = $this->_customerSession->getCustomerId();
        }
        return $this->_customerId;
    }

    /**
     * Retrieve wishlist collection
     *
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getWishlists()
    {
        return $this->_wishlistData->getCustomerWishlists($this->_getCustomerId());
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getDefaultWishlist()
    {
        return $this->_wishlistData->getDefaultWishlist();
    }

    /**
     * Retrieve currently selected wishlist
     *
     * @return Magento_Wishlist_Model_Wishlist
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
     * @param Magento_Wishlist_Model_Wishlist $wishlist
     * @return string
     */
    public function getItemCount(Magento_Wishlist_Model_Wishlist $wishlist)
    {
        $count = $this->_wishlistData->getWishlistItemCount($wishlist);
        if ($count == 1) {
            return __('1 item');
        } else {
            return __('%1 items', $count);
        }
    }

    /**
     * Build wishlist management page url
     *
     * @param Magento_Wishlist_Model_Wishlist $wishlist
     * @return string
     */
    public function getWishlistManagementUrl(Magento_Wishlist_Model_Wishlist $wishlist)
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
        return !$this->_wishlistData->isWishlistLimitReached($wishlists);
    }
}
