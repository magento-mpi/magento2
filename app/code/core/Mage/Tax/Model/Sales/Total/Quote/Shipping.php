<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Tax_Model_Sales_Total_Quote_Shipping extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Tax calculation model
     *
     * @var Mage_Tax_Model_Calculation
     */
    protected $_calculator = null;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config = null;

    /**
     * Flag which is initialized when collect method is start.
     * Is used for checking if store tax and customer tax requests are similar
     *
     * @var bool
     */
    protected $_areTaxRequestsSimilar = false;

    /**
     * Request which can be used for tax rate calculation
     *
     * @var Varien_Object
     */
    protected $_storeTaxRequest = null;
    protected $_addressTaxRequest = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->setCode('shipping');
        $this->_calculator  = Mage::getSingleton('tax/calculation');
        $this->_config      = Mage::getSingleton('tax/config');
    }

    /**
     * Collect totals information about shipping
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Shipping
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests(
            $this->_getStoreTaxRequest($address),
            $this->_getAddressTaxRequest($address)
        );
        if (!$address->getTaxShippingIsProcessed() && $this->_needSubtractShippingTax($address)) {
            $this->_processShippingAmount($address);
            $this->_config->setNeedUseShippingExcludeTax(true);
        }
        $address->setTaxShippingIsProcessed(true);
    }

    /**
     * Get request for fetching store tax rate
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Varien_Object
     */
    protected function _getStoreTaxRequest($address)
    {
        if (is_null($this->_storeTaxRequest)) {
            $this->_storeTaxRequest = $this->_calculator->getRateOriginRequest($address->getQuote()->getStore());
        }
        return $this->_storeTaxRequest;
    }

    /**
     * Get request for fetching address tax rate
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Varien_Object
     */
    protected function _getAddressTaxRequest($address)
    {
        if (is_null($this->_addressTaxRequest)) {
            $this->_addressTaxRequest = $this->_calculator->getRateRequest(
                $address,
                $address->getQuote()->getBillingAddress(),
                $address->getQuote()->getCustomerTaxClassId(),
                $address->getQuote()->getStore()
            );
        }
        return $this->_addressTaxRequest;
    }

    /**
     * Check if we need subtract store tax amount from shipping
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    protected function _needSubtractShippingTax($address)
    {
        $store = $address->getQuote()->getStore();
        if ($this->_config->shippingPriceIncludesTax($store) || $this->_config->getNeedUseShippingExcludeTax()) {
            return true;
        }
        return false;
    }

    /**
     * Calculate shipping price without store tax
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _processShippingAmount($address)
    {
        if ($this->_areTaxRequestsSimilar) {
            return $this;
        }
        $store = $address->getQuote()->getStore();
        $shippingTaxClass   = $this->_config->getShippingTaxClass($store);
        $shippingAmount     = $address->getShippingAmount();
        $baseShippingAmount = $address->getBaseShippingAmount();

        if ($shippingTaxClass) {
            $request = $this->_getStoreTaxRequest($address);
            $request->setProductClassId($shippingTaxClass);
            $rate = $this->_calculator->getRate($request);
            if ($rate) {
                $shippingTax         = $this->_calculator->calcTaxAmount($shippingAmount, $rate, true, false);
                $shippingBaseTax     = $this->_calculator->calcTaxAmount($baseShippingAmount, $rate, true, false);
                $shippingAmount     -= $shippingTax;
                $baseShippingAmount -= $shippingBaseTax;
                $address->setTotalAmount('shipping', $this->_calculator->round($shippingAmount));
                $address->setBaseTotalAmount('shipping', $this->_calculator->round($baseShippingAmount));
            }
        }
        return $this;
    }

}