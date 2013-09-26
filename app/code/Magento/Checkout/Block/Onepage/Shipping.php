<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout status
 */
class Magento_Checkout_Block_Onepage_Shipping extends Magento_Checkout_Block_Onepage_Abstract
{
    /**
     * Sales Qoute Shipping Address instance
     *
     * @var Magento_Sales_Model_Quote_Address
     */
    protected $_address = null;

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
     * Initialize shipping address step
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping', array(
            'label'     => __('Shipping Information'),
            'is_show'   => $this->isShow()
        ));

        parent::_construct();
    }

    /**
     * Return checkout method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * Return Sales Quote Address model (shipping address)
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn()) {
                $this->_address = $this->getQuote()->getShippingAddress();
            } else {
                $this->_address = $this->_addressFactory->create();
            }
        }

        return $this->_address;
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }
}
