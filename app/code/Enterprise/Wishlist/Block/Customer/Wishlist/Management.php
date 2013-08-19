<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist sidebar block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Customer_Wishlist_Management extends Magento_Core_Block_Template
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
     * Render block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::helper('Enterprise_Wishlist_Helper_Data')->isMultipleEnabled()) {
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
            $this->_customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();
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
        return Mage::helper('Enterprise_Wishlist_Helper_Data')->getCustomerWishlists($this->_getCustomerId());
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getDefaultWishlist()
    {
        return Mage::helper('Enterprise_Wishlist_Helper_Data')->getDefaultWishlist();
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
        $count = Mage::helper('Enterprise_Wishlist_Helper_Data')->getWishlistItemCount($wishlist);
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
        return !Mage::helper('Enterprise_Wishlist_Helper_Data')->isWishlistLimitReached($wishlists);
    }
}
