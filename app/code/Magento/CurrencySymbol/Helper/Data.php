<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Currency Symbol helper
 *
 * @category   Magento
 * @package    Magento_CurrencySymbol
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CurrencySymbol_Helper_Data extends Magento_Core_Helper_Data
{
    /**
     * @var Magento_CurrencySymbol_Model_System_Currencysymbol_Factory
     */
    protected $_symbolFactory;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Locale $locale
     * @param Magento_Core_Model_Date $dateModel
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_Encryption $encryptor
     * @param Magento_CurrencySymbol_Model_System_Currencysymbol_Factory $symbolFactory
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Locale $locale,
        Magento_Core_Model_Date $dateModel,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_Encryption $encryptor,
        Magento_CurrencySymbol_Model_System_Currencysymbol_Factory $symbolFactory,
        $dbCompatibleMode = true
    ) {
        $this->_symbolFactory = $symbolFactory;
        parent::__construct(
            $eventManager,
            $coreHttp,
            $context,
            $config,
            $coreStoreConfig,
            $storeManager,
            $locale,
            $dateModel,
            $appState,
            $encryptor,
            $dbCompatibleMode
        );
    }

    /**
     * Get currency display options
     *
     * @param string $baseCode
     * @return array
     */
    public function getCurrencyOptions($baseCode)
    {
        $currencyOptions = array();
        $currencySymbol = $this->_symbolFactory->create();
        if($currencySymbol) {
            $customCurrencySymbol = $currencySymbol->getCurrencySymbol($baseCode);

            if ($customCurrencySymbol) {
                $currencyOptions['symbol']  = $customCurrencySymbol;
                $currencyOptions['display'] = Zend_Currency::USE_SYMBOL;
            }
        }

        return $currencyOptions;
    }
}
