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
 */
abstract class Magento_Directory_Model_Currency_Import_Abstract
    implements Magento_Directory_Model_Currency_Import_Interface
{
    /**
     * Messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * @var Magento_Directory_Model_CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     */
    public function __construct(Magento_Directory_Model_CurrencyFactory $currencyFactory)
    {
        $this->_currencyFactory = $currencyFactory;
    }

    /**
     * Retrieve currency codes
     *
     * @return array
     */
    protected function _getCurrencyCodes()
    {
        return $this->_currencyFactory->create()->getConfigAllowCurrencies();
    }

    /**
     * Retrieve default currency codes
     *
     * @return array
     */
    protected function _getDefaultCurrencyCodes()
    {
        return $this->_currencyFactory->create()->getConfigBaseCurrencies();
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
            $this->_currencyFactory->create()
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

    /**
     * @return array
     */
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
                    $data[$currencyFrom][$currencyTo] = $this->_numberFormat(
                        $this->_convert($currencyFrom, $currencyTo)
                    );
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

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
