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
 * Wishlist view block
 */
class Magento_MultipleWishlist_Block_Info extends Magento_Wishlist_Block_Abstract
{
    /**
     * Customer model factory
     *
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Customer_Model_Session $customerSession,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        array $data = array()
    ) {
        $this->_customerFactory = $customerFactory;
        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData, $context,
            $wishlistData, $customerSession, $productFactory, $data);
    }

    /**
     * Create message block
     *
     * @return Magento_Core_Block_Abstract
     */
    public function getMessagesBlock()
    {
        return $this->getLayout()->getBlock('messages');
    }

    /**
     * Add form submission url
     *
     * @return string
     */
    public function getToCartUrl()
    {
        return $this->getUrl('wishlist/search/addtocart');
    }

    /**
     * Retrieve wishlist owner instance
     *
     * @return Magento_Customer_Model_Customer|null
     */
    public function getWishlistOwner()
    {
        /** @var Magento_Customer_Model_Customer $owner */
        $owner = $this->_customerFactory->create();
        $owner->load($this->_getWishlist()->getCustomerId());
        return $owner;
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'wishlist/search/results',
            array('_query' => array('params' => $this->_customerSession->getLastWishlistSearchParams()))
        );
    }
}
