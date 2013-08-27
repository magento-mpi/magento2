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
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Block_Currency extends Magento_Core_Block_Template
{
    /**
     * Directory url
     *
     * @var Magento_Directory_Helper_Url
     */
    protected $_directoryUrl = null;

    /**
     * @param Magento_Directory_Helper_Url $directoryUrl
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Directory_Helper_Url $directoryUrl,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_directoryUrl = $directoryUrl;
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
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes) && count($codes) > 1) {
                $rates = Mage::getModel('Magento_Directory_Model_Currency')->getCurrencyRates(
                    Mage::app()->getStore()->getBaseCurrency(),
                    $codes
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $currencies[$code] = Mage::app()->getLocale()
                            ->getTranslation($code, 'nametocurrency');
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
            // do not use Mage::app()->getStore()->getCurrentCurrencyCode() because of probability
            // to get an invalid (without base rate) currency from code saved in session
            $this->setData('current_currency_code', Mage::app()->getStore()->getCurrentCurrency()->getCode());
        }

        return $this->_getData('current_currency_code');
    }
}
