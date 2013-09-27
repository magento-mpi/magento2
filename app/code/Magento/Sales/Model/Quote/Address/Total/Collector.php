<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Address Total Collector model
 */
class Magento_Sales_Model_Quote_Address_Total_Collector extends Magento_Sales_Model_Config_Ordered
{
    /**
     * Path to sort order values of checkout totals
     */
    const XML_PATH_SALES_TOTALS_SORT = 'sales/totals_sort';

    /**
     * Total models array ordered for right display sequence
     *
     * @var array
     */
    protected $_retrievers = array();

    /**
     * Corresponding store object
     *
     * @var Magento_Core_Model_Store
     */
    protected $_store;

    /**
     * Config group for totals declaration
     *
     * @var string
     */
    protected $_configGroup = 'totals';

    /**
     * @var string
     */
    protected $_configSection = 'quote';

    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_quote_collectors';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Sales_Model_Quote_Address_TotalFactory
     */
    protected $_totalFactory;

    /**
     * Init corresponding total models
     *
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Sales_Model_Config $salesConfig
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Sales_Model_Quote_Address_TotalFactory $totalFactory
     * @param Magento_Core_Model_Store|null $store
     * @param Magento_Simplexml_Element|null $sourceData
     */
    public function __construct(
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Sales_Model_Config $salesConfig,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Sales_Model_Quote_Address_TotalFactory $totalFactory,
        $store = null,
        $sourceData = null
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_totalFactory = $totalFactory;
        parent::__construct($configCacheType, $logger, $salesConfig, $sourceData);
        $this->_store = $store ?: $storeManager->getStore();
        $this->_initModels()->_initCollectors()->_initRetrievers();
    }

    /**
     * Get total models array ordered for right calculation logic
     *
     * @return array
     */
    public function getCollectors()
    {
        return $this->_collectors;
    }

    /**
     * Get total models array ordered for right display sequence
     *
     * @return array
     */
    public function getRetrievers()
    {
        return $this->_retrievers;
    }

    /**
     * Init model class by configuration
     *
     * @param string $class
     * @param string $totalCode
     * @param array $totalConfig
     * @return Magento_Sales_Model_Quote_Address_Total_Abstract
     * @throws Magento_Core_Exception
     */
    protected function _initModelInstance($class, $totalCode, $totalConfig)
    {
        $model = $this->_totalFactory->create($class);
        if (!$model instanceof Magento_Sales_Model_Quote_Address_Total_Abstract) {
            throw new Magento_Core_Exception(
                __('The address total model should be extended from Magento_Sales_Model_Quote_Address_Total_Abstract.')
            );
        }

        $model->setCode($totalCode);
        $this->_modelsConfig[$totalCode]= $this->_prepareConfigArray($totalCode, $totalConfig);
        $this->_modelsConfig[$totalCode]= $model->processConfigArray(
            $this->_modelsConfig[$totalCode],
            $this->_store
        );

        return $model;
    }

    /**
     * Initialize retrievers array
     *
     * @return Magento_Sales_Model_Quote_Address_Total_Collector
     */
    protected function _initRetrievers()
    {
        $sorts = $this->_coreStoreConfig->getConfig(self::XML_PATH_SALES_TOTALS_SORT, $this->_store);
        foreach ($sorts as $code => $sortOrder) {
            if (isset($this->_models[$code])) {
                // Reserve enough space for collisions
                $retrieverId = 100 * (int) $sortOrder;
                // Check if there is a retriever with such id and find next available position if needed
                while (isset($this->_retrievers[$retrieverId])) {
                    $retrieverId++;
                }
                $this->_retrievers[$retrieverId] = $this->_models[$code];
            }
        }
        ksort($this->_retrievers);
        $notSorted = array_diff(array_keys($this->_models), array_keys($sorts));
        foreach ($notSorted as $code) {
            $this->_retrievers[] = $this->_models[$code];
        }
        return $this;
    }
}
