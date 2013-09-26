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
 * Currency dropdown block
 */
class Magento_Directory_Block_Currency extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Directory url
     *
     * @var Magento_Directory_Helper_Url
     */
    protected $_directoryUrl = null;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Directory_Model_CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @param Magento_Directory_Helper_Url $directoryUrl
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param array $data
     */
    public function __construct(
        Magento_Directory_Helper_Url $directoryUrl,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        array $data = array()
    ) {
        $this->_directoryUrl = $directoryUrl;
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
        $this->_currencyFactory = $currencyFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve count of currencies
     * Return 0 if only one currency
     *
     * @return int
     */
    public function getCurrencyCount()
    {
        return count($this->getCurrencies());
    }

    /**
     * Retrieve currencies array
     * Return array: code => currency name
     * Return empty array if only one currency
     *
     * @return array
     */
    public function getCurrencies()
    {
        $currencies = $this->getData('currencies');
        if (is_null($currencies)) {
            $currencies = array();
            $codes = $this->_storeManager->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes) && count($codes) > 1) {
                $rates = $this->_currencyFactory->create()->getCurrencyRates(
                    $this->_storeManager->getStore()->getBaseCurrency(),
                    $codes
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $currencies[$code] = $this->_locale->getTranslation($code, 'nametocurrency');
                    }
                }
            }

            $this->setData('currencies', $currencies);
        }
        return $currencies;
    }

    /**
     * Retrieve Currency Swith URL
     *
     * @return string
     */
    public function getSwitchUrl()
    {
        return $this->getUrl('directory/currency/switch');
    }

    /**
     * Return URL for specified currency to switch
     *
     * @param string $code Currency code
     * @return string
     */
    public function getSwitchCurrencyUrl($code)
    {
        return $this->_directoryUrl->getSwitchCurrencyUrl(array('currency' => $code));
    }

    /**
     * Retrieve Current Currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_getData('current_currency_code'))) {
            // do not use $this->_storeManager->getStore()->getCurrentCurrencyCode() because of probability
            // to get an invalid (without base rate) currency from code saved in session
            $this->setData('current_currency_code', $this->_storeManager->getStore()->getCurrentCurrency()->getCode());
        }

        return $this->_getData('current_currency_code');
    }

    /**
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
}
