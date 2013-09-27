<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order create data
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Data extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * @var Magento_Directory_Model_CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_currencyFactory = $currencyFactory;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $data);
    }

    /**
     * Retrieve avilable currency codes
     *
     * @return unknown
     */
    public function getAvailableCurrencies()
    {
        $dirtyCodes = $this->getStore()->getAvailableCurrencyCodes();
        $codes = array();
        if (is_array($dirtyCodes) && count($dirtyCodes)) {
            $rates = $this->_currencyFactory->create()->getCurrencyRates(
                $this->_storeManager->getStore()->getBaseCurrency(),
                $dirtyCodes
            );
            foreach ($dirtyCodes as $code) {
                if (isset($rates[$code]) || $code == $this->_storeManager->getStore()->getBaseCurrencyCode()) {
                    $codes[] = $code;
                }
            }
        }
        return $codes;
    }

    /**
     * Retrieve curency name by code
     *
     * @param   string $code
     * @return  string
     */
    public function getCurrencyName($code)
    {
        return $this->_locale->currency($code)->getName();
    }

    /**
     * Retrieve curency name by code
     *
     * @param   string $code
     * @return  string
     */
    public function getCurrencySymbol($code)
    {
        $currency = $this->_locale->currency($code);
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    /**
     * Retrieve current order currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->getStore()->getCurrentCurrencyCode();
    }

}
