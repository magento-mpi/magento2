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
class Magento_MultipleWishlist_Block_Behaviour extends Magento_Core_Block_Template
{
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
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_MultipleWishlist_Helper_Data $wishlistData
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
     * Retrieve wishlists items
     *
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
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
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getDefaultWishlist()
    {
        return $this->_wishlistData->getDefaultWishlist();
    }

    /**
     * Check whether customer reached wishlist limit
     *
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection
     * @return bool
     */
    public function canCreateWishlists($wishlistList)
    {
        $customerId = $this->_customerSession->getCustomerId();
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
