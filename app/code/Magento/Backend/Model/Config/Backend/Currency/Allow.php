<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config Directory currency backend model
 * Allows dispatching before and after events for each controller action
 */
class Magento_Backend_Model_Config_Backend_Currency_Allow extends Magento_Backend_Model_Config_Backend_Currency_Abstract
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_locale = $locale;
        parent::__construct($context, $registry, $storeManager, $config, $coreStoreConfig, $resource,
            $resourceCollection, $data);
    }

    /**
     * Check is isset default display currency in allowed currencies
     * Check allowed currencies is available in installed currencies
     *
     * @return Magento_Backend_Model_Config_Backend_Currency_Allow
     * @throws Magento_Core_Exception
     */
    protected function _afterSave()
    {
        $exceptions = array();
        foreach ($this->_getAllowedCurrencies() as $currencyCode) {
            if (!in_array($currencyCode, $this->_getInstalledCurrencies())) {
                $exceptions[] = __('Selected allowed currency "%1" is not available in installed currencies.',
                    $this->_locale->currency($currencyCode)->getName()
                );
            }
        }

        if (!in_array($this->_getCurrencyDefault(), $this->_getAllowedCurrencies())) {
            $exceptions[] = __('Default display currency "%1" is not available in allowed currencies.',
                $this->_locale->currency($this->_getCurrencyDefault())->getName()
            );
        }

        if ($exceptions) {
            throw new Magento_Core_Exception(join("\n", $exceptions));
        }

        return $this;
    }
}
