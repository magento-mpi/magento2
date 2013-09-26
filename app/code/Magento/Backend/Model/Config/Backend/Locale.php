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
 * Config locale allowed currencies backend
 */
class Magento_Backend_Model_Config_Backend_Locale extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Core_Model_Resource_Config_Data_CollectionFactory
     */
    protected $_configsFactory;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Core_Model_Website_Factory
     */
    protected $_websiteFactory;

    /**
     * @var Magento_Core_Model_StoreFactory
     */
    protected $_storeFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Config_Data_CollectionFactory $configsFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_Website_Factory $websiteFactory
     * @param Magento_Core_Model_StoreFactory $storeFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Config_Data_CollectionFactory $configsFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_Website_Factory $websiteFactory,
        Magento_Core_Model_StoreFactory $storeFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configsFactory = $configsFactory;
        $this->_locale = $locale;
        $this->_websiteFactory = $websiteFactory;
        $this->_storeFactory = $storeFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * @return Magento_Backend_Model_Config_Backend_Locale
     * @throws Magento_Core_Exception
     */
    protected function _afterSave()
    {
        /** @var $collection Magento_Core_Model_Resource_Config_Data_Collection */
        $collection = $this->_configsFactory->create();
        $collection->addPathFilter('currency/options');

        $values     = explode(',', $this->getValue());
        $exceptions = array();

        foreach ($collection as $data) {
            $match = false;
            $scopeName = __('Default scope');

            if (preg_match('/(base|default)$/', $data->getPath(), $match)) {
                if (!in_array($data->getValue(), $values)) {
                    $currencyName = $this->_locale->currency($data->getValue())->getName();
                    if ($match[1] == 'base') {
                        $fieldName = __('Base currency');
                    } else {
                        $fieldName = __('Display default currency');
                    }

                    switch ($data->getScope()) {
                        case 'default':
                            $scopeName = __('Default scope');
                            break;

                        case 'website':
                            /** @var $website Magento_Core_Model_Website */
                            $website = $this->_websiteFactory->create();
                            $websiteName = $website->load($data->getScopeId())->getName();
                            $scopeName = __('website(%1) scope', $websiteName);
                            break;

                        case 'store':
                            /** @var $store Magento_Core_Model_Store */
                            $store = $this->_storeFactory->create();
                            $storeName = $store->load($data->getScopeId())->getName();
                            $scopeName = __('store(%1) scope', $storeName);
                            break;
                    }

                    $exceptions[] = __('Currency "%1" is used as %2 in %3.', $currencyName, $fieldName, $scopeName);
                }
            }
        }
        if ($exceptions) {
            throw new Magento_Core_Exception(join("\n", $exceptions));
        }

        return $this;
    }
}
