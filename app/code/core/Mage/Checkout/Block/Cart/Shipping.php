<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Abstract
{
    protected $_carriers = null;
    protected $_rates = array();
    protected $_address = array();

    public function getEstimateRates()
    {
        if (empty($this->_rates)) {
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }
        return $this->_rates;
    }

    /**
     * Get address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    public function getEstimateCountryId()
    {
        return $this->getAddress()->getCountryId();
    }

    public function getEstimatePostcode()
    {
        return $this->getAddress()->getPostcode();
    }

    public function getEstimateCity()
    {
        return $this->getAddress()->getCity();
    }

    public function getEstimateRegionId()
    {
        return $this->getAddress()->getRegionId();
    }

    public function getEstimateRegion()
    {
        return $this->getAddress()->getRegion();
    }

    public function getCityActive()
    {
        return (bool)Mage::getStoreConfig('carriers/dhl/active');
    }

    public function getStateActive()
    {
        return (bool)Mage::getStoreConfig('carriers/dhl/active') || (bool)Mage::getStoreConfig('carriers/tablerate/active');
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->convertPrice($price, true);
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->formatPrice($this->helper('Mage_Tax_Helper_Data')->getShippingPrice(
            $price,
            $flag,
            $this->getAddress(),
            $this->getQuote()->getCustomerTaxClassId()
        ));
    }

    /**
     * Obtain available carriers instances
     *
     * @return array
     */
    public function getCarriers()
    {
        if (null === $this->_carriers) {
            $this->_carriers = array();
            $this->getEstimateRates();
            foreach ($this->_rates as $rateGroup) {
                if (!empty($rateGroup)) {
                    foreach ($rateGroup as $rate) {
                        $this->_carriers[] = $rate->getCarrierInstance();
                    }
                }
            }
        }
        return $this->_carriers;
    }

    /**
     * Check if one of carriers require state/province
     *
     * @return bool
     */
    public function isStateProvinceRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isStateProvinceRequired()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if one of carriers require city
     *
     * @return bool
     */
    public function isCityRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isCityRequired()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if one of carriers require zip code
     *
     * @return bool
     */
    public function isZipCodeRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isZipCodeRequired()) {
                return true;
            }
        }
        return false;
    }
}
