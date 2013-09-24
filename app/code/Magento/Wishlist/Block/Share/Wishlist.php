<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist block shared items
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Block_Share_Wishlist extends Magento_Wishlist_Block_Abstract
{
    /**
     * Customer instance
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        array $data = array()
    ) {
        $this->_customerFactory = $customerFactory;
        parent::__construct(
            $coreRegistry,
            $wishlistData,
            $taxData,
            $catalogData,
            $coreData,
            $context,
            $storeManager,
            $customerSession,
            $productFactory,
            $data
        );
    }

    /**
     * Prepare global layout
     *
     * @return Magento_Wishlist_Block_Share_Wishlist
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->getHeader());
        }
        return $this;
    }

    /**
     * Retrieve Shared Wishlist Customer instance
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getWishlistCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = $this->_customerFactory->create()
                ->load($this->_getWishlist()->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Retrieve Page Header
     *
     * @return string
     */
    public function getHeader()
    {
        return __("%1's Wish List", $this->escapeHtml($this->getWishlistCustomer()->getFirstname()));
    }
}
