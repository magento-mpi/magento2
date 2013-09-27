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
 * Configuration class for ordered items
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Sales_Model_Config_Ordered extends Magento_Core_Model_Config_Base
{
    /**
     * Cache key for collectors
     *
     * @var string|null
     */
    protected $_collectorsCacheKey = null;

    /**
     * Configuration group where to collect registered totals
     *
     * @var string
     */
    protected $_configGroup;

    /**
     * Configuration section where to collect registered totals
     *
     * @var string
     */
    protected $_configSection;

    /**
     * Prepared models
     *
     * @var array
     */
    protected $_models = array();

    /**
     * Models configuration
     *
     * @var array
     */
    protected $_modelsConfig = array();

    /**
     * Sorted models
     *
     * @var array
     */
    protected $_collectors = array();

    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_Sales_Model_Config
     */
    protected $_salesConfig;

    /**
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Sales_Model_Config $salesConfig
     * @param Magento_Simplexml_Element $sourceData
     */
    public function __construct(
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Logger $logger,
        Magento_Sales_Model_Config $salesConfig,
        $sourceData = null
    ) {
        parent::__construct($sourceData);
        $this->_configCacheType = $configCacheType;
        $this->_logger = $logger;
        $this->_salesConfig = $salesConfig;
    }

    /**
     * Initialize total models configuration and objects
     *
     * @return Magento_Sales_Model_Config_Ordered
     */
    protected function _initModels()
    {
        $totals = $this->_salesConfig->getGroupTotals($this->_configSection, $this->_configGroup);
        foreach ($totals as $totalCode => $totalConfig) {
            $class = $totalConfig['instance'];
            if (!empty($class)) {
                $this->_models[$totalCode] = $this->_initModelInstance($class, $totalCode, $totalConfig);
            }
        }
        return $this;
    }

    /**
     * Init model class by configuration
     *
     * @abstract
     * @param string $class
     * @param string $totalCode
     * @param array $totalConfig
     * @return mixed
     */
    abstract protected function _initModelInstance($class, $totalCode, $totalConfig);

    /**
     * Prepare configuration array for total model
     *
     * @param   string $code
     * @param   Magento_Core_Model_Config_Element $totalConfig
     * @return  array
     */
    protected function _prepareConfigArray($code, $totalConfig)
    {
        $totalConfig = (array)$totalConfig;
        $totalConfig['_code'] = $code;
        return $totalConfig;
    }

    /**
     * Aggregate before/after information from all items and sort totals based on this data
     *
     * @param array $config
     * @return array
     */
    protected function _getSortedCollectorCodes(array $config)
    {
        // invoke simple sorting if the first element contains the "sort_order" key
        reset($config);
        $element = current($config);
        if (isset($element['sort_order'])) {
            uasort($config, array($this, '_compareSortOrder'));
        }
        $result = array_keys($config);
        return $result;
    }

    /**
     * Initialize collectors array.
     * Collectors array is array of total models ordered based on configuration settings
     *
     * @return  Magento_Sales_Model_Config_Ordered
     */
    protected function _initCollectors()
    {
        $sortedCodes = array();
        $cachedData = $this->_configCacheType->load($this->_collectorsCacheKey);
        if ($cachedData) {
            $sortedCodes = unserialize($cachedData);
        }
        if (!$sortedCodes) {
            $sortedCodes = $this->_getSortedCollectorCodes($this->_modelsConfig);
            $this->_configCacheType->save(serialize($sortedCodes), $this->_collectorsCacheKey);
        }
        foreach ($sortedCodes as $code) {
            $this->_collectors[$code] = $this->_models[$code];
        }

        return $this;
    }

    /**
     * Callback that uses sort_order for comparison
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _compareSortOrder($a, $b)
    {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }
        if ($a['sort_order'] > $b['sort_order']) {
            $res = 1;
        } elseif ($a['sort_order'] < $b['sort_order']) {
            $res = -1;
        } else {
            $res = 0;
        }
        return $res;
    }
}
