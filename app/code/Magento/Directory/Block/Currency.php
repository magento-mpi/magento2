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
namespace Magento\Directory\Block;

class Currency extends \Magento\View\Element\Template
{
    /**
     * Directory url
     *
     * @var \Magento\Directory\Helper\Url
     */
    protected $_directoryUrl = null;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Url $directoryUrl
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Url $directoryUrl,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        array $data = array()
    ) {
        $this->_directoryUrl = $directoryUrl;
        $this->_currencyFactory = $currencyFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
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
