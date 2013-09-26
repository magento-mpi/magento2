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
 * Currency model
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Model_Currency extends Magento_Core_Model_Abstract
{
    /**
     * CONFIG path constants
    */
    const XML_PATH_CURRENCY_ALLOW   = 'currency/options/allow';
    const XML_PATH_CURRENCY_DEFAULT = 'currency/options/default';
    const XML_PATH_CURRENCY_BASE    = 'currency/options/base';

    protected $_filter;

    /**
     * Currency Rates
     *
     * @var array
     */
    protected $_rates;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryHelper;

    /**
     * @var Magento_Directory_Model_Currency_FilterFactory
     */
    protected $_currencyFilterFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Directory_Helper_Data $directoryHelper
     * @param Magento_Directory_Model_Currency_FilterFactory $currencyFilterFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Directory_Helper_Data $directoryHelper,
        Magento_Directory_Model_Currency_FilterFactory $currencyFilterFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $resource, $resourceCollection, $data
        );
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->_directoryHelper = $directoryHelper;
        $this->_currencyFilterFactory = $currencyFilterFactory;
    }

    protected function _construct()
    {
        $this->_init('Magento_Directory_Model_Resource_Currency');
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_getData('currency_code');
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->_getData('currency_code');
    }

    /**
     * Currency Rates getter
     *
     * @return array
     */
    public function getRates()
    {
        return $this->_rates;
    }

    /**
     * Currency Rates setter
     *
     * @param array Currency Rates
     * @return Magento_Directory_Model_Currency
     */
    public function setRates(array $rates)
    {
        $this->_rates = $rates;
        return $this;
    }

    /**
     * Loading currency data
     *
     * @param   string $id
     * @param   string $field
     * @return  Magento_Directory_Model_Currency
     */
    public function load($id, $field = null)
    {
        $this->unsRate();
        $this->setData('currency_code', $id);
        return $this;
    }

    /**
     * Get currency rate (only base => allowed)
     *
     * @param string $toCurrency
     * @return double
     * @throws Magento_Directory_Exception
     */
    public function getRate($toCurrency)
    {
        if (is_string($toCurrency)) {
            $code = $toCurrency;
        } elseif ($toCurrency instanceof Magento_Directory_Model_Currency) {
            $code = $toCurrency->getCurrencyCode();
        } else {
            throw new Magento_Directory_Exception(__('Please correct the target currency.'));
        }
        $rates = $this->getRates();
        if (!isset($rates[$code])) {
            $rates[$code] = $this->_getResource()->getRate($this->getCode(), $toCurrency);
            $this->setRates($rates);
        }
        return $rates[$code];
    }

    /**
     * Get currency rate (base=>allowed or allowed=>base)
     *
     * @param string $toCurrency
     * @return double
     * @throws Magento_Directory_Exception
     */
    public function getAnyRate($toCurrency)
    {
        if (is_string($toCurrency)) {
            $code = $toCurrency;
        } elseif ($toCurrency instanceof Magento_Directory_Model_Currency) {
            $code = $toCurrency->getCurrencyCode();
        } else {
            throw new Magento_Directory_Exception(__('Please correct the target currency.'));
        }
        $rates = $this->getRates();
        if (!isset($rates[$code])) {
            $rates[$code] = $this->_getResource()->getAnyRate($this->getCode(), $toCurrency);
            $this->setRates($rates);
        }
        return $rates[$code];
    }

    /**
     * Convert price to currency format
     *
     * @param   double $price
     * @param   string $toCurrency
     * @return  double
     * @throws Exception
     */
    public function convert($price, $toCurrency = null)
    {
        if (is_null($toCurrency)) {
            return $price;
        } elseif ($rate = $this->getRate($toCurrency)) {
            return $price * $rate;
        }

        throw new Exception(__('Undefined rate from "%1-%2".', $this->getCode(), $toCurrency->getCode()));
    }

    /**
     * Get currency filter
     *
     * @return Magento_Directory_Model_Currency_Filter
     */
    public function getFilter()
    {
        if (!$this->_filter) {
            $this->_filter = $this->_currencyFilterFactory->create(array('code' => $this->getCode()));
        }

        return $this->_filter;
    }

    /**
     * Format price to currency format
     *
     * @param double $price
     * @param array $options
     * @param bool $includeContainer
     * @param bool $addBrackets
     * @return string
     */
    public function format($price, $options = array(), $includeContainer = true, $addBrackets = false)
    {
        return $this->formatPrecision($price, 2, $options, $includeContainer, $addBrackets);
    }

    /**
     * Apply currency format to number with specific rounding precision
     *
     * @param   float $price
     * @param   int $precision
     * @param   array $options
     * @param   bool $includeContainer
     * @param   bool $addBrackets
     * @return  string
     */
    public function formatPrecision(
        $price,
        $precision,
        $options = array(),
        $includeContainer = true,
        $addBrackets = false
    ) {
        if (!isset($options['precision'])) {
            $options['precision'] = $precision;
        }
        if ($includeContainer) {
            return '<span class="price">' . ($addBrackets ? '[' : '')
                . $this->formatTxt($price, $options) . ($addBrackets ? ']' : '') . '</span>';
        }
        return $this->formatTxt($price, $options);
    }

    /**
     * @param float $price
     * @param array $options
     * @return string
     */
    public function formatTxt($price, $options = array())
    {
        if (!is_numeric($price)) {
            $price = $this->_locale->getNumber($price);
        }
        /**
         * Fix problem with 12 000 000, 1 200 000
         *
         * %f - the argument is treated as a float, and presented as a floating-point number (locale aware).
         * %F - the argument is treated as a float, and presented as a floating-point number (non-locale aware).
         */
        $price = sprintf("%F", $price);
        return $this->_locale->currency($this->getCode())->toCurrency($price, $options);
    }

    /**
     * @return string
     */
    public function getOutputFormat()
    {
        $formatted = $this->formatTxt(0);
        $number = $this->formatTxt(0, array('display' => Zend_Currency::NO_SYMBOL));
        return str_replace($number, '%s', $formatted);
    }

    /**
     * Retrieve allowed currencies according to config
     *
     */
    public function getConfigAllowCurrencies()
    {
        $allowedCurrencies = $this->_getResource()->getConfigCurrencies($this, self::XML_PATH_CURRENCY_ALLOW);
        $appBaseCurrencyCode = $this->_directoryHelper->getBaseCurrencyCode();
        if (!in_array($appBaseCurrencyCode, $allowedCurrencies)) {
            $allowedCurrencies[] = $appBaseCurrencyCode;
        }
        foreach ($this->_storeManager->getStores() as $store) {
            $code = $store->getBaseCurrencyCode();
            if (!in_array($code, $allowedCurrencies)) {
                $allowedCurrencies[] = $code;
            }
        }

        return $allowedCurrencies;
    }

    /**
     * Retrieve default currencies according to config
     *
     */
    public function getConfigDefaultCurrencies()
    {
        $defaultCurrencies = $this->_getResource()->getConfigCurrencies($this, self::XML_PATH_CURRENCY_DEFAULT);
        return $defaultCurrencies;
    }


    public function getConfigBaseCurrencies()
    {
        $defaultCurrencies = $this->_getResource()->getConfigCurrencies($this, self::XML_PATH_CURRENCY_BASE);
        return $defaultCurrencies;
    }

    /**
     * Retrieve currency rates to other currencies
     *
     * @param string $currency
     * @param array $toCurrencies
     * @return array
     */
    public function getCurrencyRates($currency, $toCurrencies=null)
    {
        if ($currency instanceof Magento_Directory_Model_Currency) {
            $currency = $currency->getCode();
        }
        $data = $this->_getResource()->getCurrencyRates($currency, $toCurrencies);
        return $data;
    }

    /**
     * Save currency rates
     *
     * @param array $rates
     * @return object
     */
    public function saveRates($rates)
    {
        $this->_getResource()->saveRates($rates);
        return $this;
    }
}
