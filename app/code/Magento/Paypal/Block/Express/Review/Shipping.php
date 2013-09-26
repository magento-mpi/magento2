<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Express Onepage checkout block for Shipping Address
 */
class Magento_Paypal_Block_Express_Review_Shipping extends Magento_Checkout_Block_Onepage_Shipping
{
    /**
     * @var Magento_Sales_Model_Quote_AddressFactory
     */
    protected $_addressFactory;

    /**
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $resourceSession
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Sales_Model_Quote_AddressFactory $addressFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Checkout_Model_Session $resourceSession,
        Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory,
        Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Sales_Model_Quote_AddressFactory $addressFactory,
        array $data = array()
    ) {
        $this->_addressFactory = $addressFactory;
        parent::__construct($configCacheType, $coreData, $context, $customerSession, $resourceSession,
            $countryCollFactory, $regionCollFactory, $storeManager, $data);
    }


    /**
     * Return Sales Quote Address model (shipping address)
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn() || $this->getQuote()->getShippingAddress()) {
                $this->_address = $this->getQuote()->getShippingAddress();
            } else {
                $this->_address = $this->_addressFactory->create();
            }
        }

        return $this->_address;
    }
}
