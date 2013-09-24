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
 * Wishlist rss feed block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Rss extends Magento_Rss_Block_Wishlist
{
    /**
     * Wishlist factory
     *
     * @var Magento_Wishlist_Model_WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * Customer factory
     *
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Wishlist_Model_WishlistFactory $wishlistFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Wishlist_Model_WishlistFactory $wishlistFactory,
        array $data = array()
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_wishlistFactory = $wishlistFactory;
        parent::__construct($coreRegistry, $wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Retrieve Wishlist model
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = $this->_wishlistFactory->create();
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
            } else {
                if ($this->_getCustomer()->getId()) {
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
            /** @var Magento_Customer_Model_Customer $customer */
            $customer = $this->_customerFactory->create();
            $customer->load($this->_getWishlist()->getCustomerId());
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
