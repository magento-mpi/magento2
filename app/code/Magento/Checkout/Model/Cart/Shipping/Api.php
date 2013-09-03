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
 * Shopping cart api
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Checkout_Model_Cart_Shipping_Api extends Magento_Checkout_Model_Api_Resource
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Api_Helper_Data $apiHelper
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Api_Helper_Data $apiHelper,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        parent::__construct($apiHelper);
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_ignoredAttributeCodes['quote_shipping_rate'] = array('address_id', 'created_at', 'updated_at', 'rate_id', 'carrier_sort_order');
    }

    /**
     * Set an Shipping Method for Shopping Cart
     *
     * @param  $quoteId
     * @param  $shippingMethod
     * @param  $store
     * @return bool
     */
    public function setShippingMethod($quoteId, $shippingMethod, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $quoteShippingAddress = $quote->getShippingAddress();
        if(is_null($quoteShippingAddress->getId()) ) {
            $this->_fault("shipping_address_is_not_set");
        }

        $rate = $quote->getShippingAddress()->collectShippingRates()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            $this->_fault('shipping_method_is_not_available');
        }

        try {
            $quote->getShippingAddress()->setShippingMethod($shippingMethod);
            $quote->collectTotals()->save();
        } catch(Magento_Core_Exception $e) {
            $this->_fault('shipping_method_is_not_set', $e->getMessage());
        }

        return true;
    }

    /**
     * Get list of available shipping methods
     *
     * @param  $quoteId
     * @param  $store
     * @return array
     */
    public function getShippingMethodsList($quoteId, $store=null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $quoteShippingAddress = $quote->getShippingAddress();
        if (is_null($quoteShippingAddress->getId())) {
            $this->_fault("shipping_address_is_not_set");
        }

        try {
            $quoteShippingAddress->collectShippingRates()->save();
            $groupedRates = $quoteShippingAddress->getGroupedAllShippingRates();

            $ratesResult = array();
            foreach ($groupedRates as $carrierCode => $rates ) {
                $carrierName = $carrierCode;
                if (!is_null($this->_coreStoreConfig->getConfig('carriers/'.$carrierCode.'/title'))) {
                    $carrierName = $this->_coreStoreConfig->getConfig('carriers/'.$carrierCode.'/title');
                }

                foreach ($rates as $rate) {
                    $rateItem = $this->_getAttributes($rate, "quote_shipping_rate");
                    $rateItem['carrierName'] = $carrierName;
                    $ratesResult[] = $rateItem;
                    unset($rateItem);
                }
            }
        } catch (Magento_Core_Exception $e) {
            $this->_fault('shipping_methods_list_could_not_be_retrived', $e->getMessage());
        }

        return $ratesResult;
    }


}
