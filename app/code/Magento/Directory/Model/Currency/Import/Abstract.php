<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract model for import currency
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Directory_Model_Currency_Import_Abstract
{
    /**
     * Retrieve currency codes
     *
     * @return array
     */
    protected function _getCurrencyCodes()
    {
        return Mage::getModel('Magento_Directory_Model_Currency')->getConfigAllowCurrencies();
    }

    /**
     * Retrieve default currency codes
     *
     * @return array
     */
    protected function _getDefaultCurrencyCodes()
    {
        return Mage::getModel('Magento_Directory_Model_Currency')->getConfigBaseCurrencies();
    }

    /**
     * Retrieve rate
     *
     * @param   string $currencyFrom
     * @param   string $currencyTo
     * @return  float
     */
    abstract protected function _convert($currencyFrom, $currencyTo);

    /**
     * Saving currency rates
     *
     * @param   array $rates
     * @return  Magento_Directory_Model_Currency_Import_Abstract
     */
    protected function _saveRates($rates)
    {
        foreach ($rates as $currencyCode => $currencyRates) {
            Mage::getModel('Magento_Directory_Model_Currency')
                ->setId($currencyCode)
                ->setRates($currencyRates)
                ->save();
        }
        return $this;
    }

    /**
     * Import rates
     *
     * @return Magento_Directory_Model_Currency_Import_Abstract
     */
    public function importRates()
    {
        $data = $this->fetchRates();
        $this->_saveRates($data);
        return $this;
    }

    public function fetchRates()
    {
        $data = array();
        $currencies = $this->_getCurrencyCodes();
        $defaultCurrencies = $this->_getDefaultCurrencyCodes();
        @set_time_limit(0);
        foreach ($defaultCurrencies as $currencyFrom) {
            if (!isset($data[$currencyFrom])) {
                $data[$currencyFrom] = array();
            }

            foreach ($currencies as $currencyTo) {
                if ($currencyFrom == $currencyTo) {
                    $data[$currencyFrom][$currencyTo] = $this->_numberFormat(1);
                }
                else {
                    $data[$currencyFrom][$currencyTo] = $this->_numberFormat($this->_convert($currencyFrom, $currencyTo));
                }
            }
            ksort($data[$currencyFrom]);
        }

        return $data;
    }

    protected function _numberFormat($number)
    {
        return $number;
    }

    public function getMessages()
    {
        return $this->_messages;
    }
}
