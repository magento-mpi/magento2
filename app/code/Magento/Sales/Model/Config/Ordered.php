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
namespace Magento\Sales\Model\Config;

abstract class Ordered extends \Magento\Core\Model\Config\Base
{
    /**
     * Cache key for collectors
     *
     * @var string|null
     */
    protected $_collectorsCacheKey = null;

    /**
     * Configuration path where to collect registered totals
     *
     * @var string|null
     */
    protected $_totalsConfigNode = null;

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
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Simplexml\Element|null $sourceData
     */
    public function __construct(
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\Logger $logger,
        $sourceData = null
    ) {
        parent::__construct($sourceData);
        $this->_configCacheType = $configCacheType;
        $this->_logger = $logger;
    }

    /**
     * Initialize total models configuration and objects
     *
     * @return \Magento\Sales\Model\Config\Ordered
     */
    protected function _initModels()
    {
        $totalsConfig = $this->getNode($this->_totalsConfigNode);

        foreach ($totalsConfig->children() as $totalCode => $totalConfig) {
            $class = $totalConfig->getClassName();
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
     * @param   \Magento\Core\Model\Config\Element $totalConfig
     * @return  array
     */
    protected function _prepareConfigArray($code, $totalConfig)
    {
        $totalConfig = (array)$totalConfig;
        if (isset($totalConfig['before'])) {
            $totalConfig['before'] = explode(',', $totalConfig['before']);
        } else {
            $totalConfig['before'] = array();
        }
        if (isset($totalConfig['after'])) {
            $totalConfig['after'] = explode(',', $totalConfig['after']);
        } else {
            $totalConfig['after'] = array();
        }
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
            $result = array_keys($config);
        } else {
            $result = array_keys($config);
            // Move all totals with before specification in front of related total
            foreach ($config as $code => &$data) {
                foreach ($data['before'] as $positionCode) {
                    if (!isset($config[$positionCode])) {
                        continue;
                    }
                    if (!in_array($code, $config[$positionCode]['after'], true)) {
                        // Also add additional after condition for related total,
                        // to keep it always after total with before value specified
                        $config[$positionCode]['after'][] = $code;
                    }
                    $currentPosition = array_search($code, $result, true);
                    $desiredPosition = array_search($positionCode, $result, true);
                    if ($currentPosition > $desiredPosition) {
                        // Only if current position is not corresponding to before condition
                        array_splice($result, $currentPosition, 1); // Removes existent
                        array_splice($result, $desiredPosition, 0, $code); // Add at new position
                    }
                }
            }
            // Sort out totals with after position specified
            foreach ($config as $code => &$data) {
                $maxAfter = null;
                $currentPosition = array_search($code, $result, true);

                foreach ($data['after'] as $positionCode) {
                    $maxAfter = max($maxAfter, array_search($positionCode, $result, true));
                }

                if ($maxAfter !== null && $maxAfter > $currentPosition) {
                    // Moves only if it is in front of after total
                    array_splice($result, $maxAfter + 1, 0, $code); // Add at new position
                    array_splice($result, $currentPosition, 1); // Removes existent
                }
            }
        }
        return $result;
    }

    /**
     * Initialize collectors array.
     * Collectors array is array of total models ordered based on configuration settings
     *
     * @return  \Magento\Sales\Model\Config\Ordered
     */
    protected function _initCollectors()
    {
        $sortedCodes = array();
        $cachedData = $this->_configCacheType->load($this->_collectorsCacheKey);
        if ($cachedData) {
            $sortedCodes = unserialize($cachedData);
        }
        if (!$sortedCodes) {
            try {
                self::validateCollectorDeclarations($this->_modelsConfig);
            } catch (\Exception $e) {
                $this->_logger->logException($e);
            }
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

    /**
     * Validate specified configuration array as sales totals declaration
     *
     * If there are contradictions, the totals cannot be sorted correctly. Possible contradictions:
     * - A relation between totals leads to cycles
     * - Two relations combined lead to cycles
     *
     * @param array $config
     * @throws \Magento\Exception
     */
    public static function validateCollectorDeclarations($config)
    {
        $before = self::_instantiateGraph($config, 'before');
        $after  = self::_instantiateGraph($config, 'after');
        foreach ($after->getRelations(\Magento\Data\Graph::INVERSE) as $from => $relations) {
            foreach ($relations as $to) {
                $before->addRelation($from, $to);
            }
        }
        $cycle = $before->findCycle();
        if ($cycle) {
            throw new \Magento\Exception(sprintf(
                'Found cycle in sales total declarations: %s', implode(' -> ', $cycle)
            ));
        }
    }

    /**
     * Parse "config" array by specified key and instantiate a graph based on that
     *
     * @param array $config
     * @param string $key
     * @return \Magento\Data\Graph
     */
    private static function _instantiateGraph($config, $key)
    {
        $nodes = array_keys($config);
        $graph = array();
        foreach ($config as $from => $row) {
            foreach ($row[$key] as $to) {
                $graph[] = array($from, $to);
            }
        }
        return new \Magento\Data\Graph($nodes, $graph);
    }
}
