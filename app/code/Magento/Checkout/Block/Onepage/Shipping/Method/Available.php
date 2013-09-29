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
 *
 * @category   Magento
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Shipping_Method_Available extends Magento_Checkout_Block_Onepage_Abstract
{
    protected $_rates;
    protected $_address;

    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $resourceSession
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Tax_Helper_Data $taxData
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
        Magento_Tax_Helper_Data $taxData,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        parent::__construct($configCacheType, $coreData, $context, $customerSession, $resourceSession,
            $countryCollFactory, $regionCollFactory, $storeManager, $data);
    }

    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();
            $this->_rates = $this->getAddress()->getGroupedAllShippingRates();        }

        return $this->_rates;
    }

    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    public function getCarrierName($carrierCode)
    {
        if ($name = $this->_storeConfig->getConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->getQuote()->getStore()->convertPrice(
            $this->_taxData->getShippingPrice($price, $flag, $this->getAddress()),
            true
        );
    }
}
