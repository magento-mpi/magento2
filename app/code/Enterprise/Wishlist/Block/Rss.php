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
 * Wishlist rss feed block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Rss extends Magento_Rss_Block_Wishlist
{
    /**
     * Wishlist data
     *
     * @var Enterprise_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Enterprise_Wishlist_Helper_Data $wishlistData
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Wishlist_Helper_Data $wishlistData,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Retrieve Wishlist model
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist');
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
            } else {
                if($this->_getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Build feed title
     *
     * @return string
     */
    protected function _getTitle()
    {
        $customer = $this->_getCustomer();
        if ($this->_getWishlist()->getCustomerId() !== $customer->getId()) {
            $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($this->_getWishlist()->getCustomerId());
        }
        if ($this->_wishlistData->isWishlistDefault($this->_getWishlist())
            && $this->_getWishlist()->getName() == $this->_wishlistData->getDefaultWishlistName()
        ) {
            return __("%1's Wish List", $customer->getName());
        } else {
            return __("%1's Wish List (%2)", $customer->getName(), $this->_getWishlist()->getName());
        }
    }
}
