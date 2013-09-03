<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Config;

class Config implements \Magento\ObjectManager\Config
{
    /**
     * Config cache
     *
     * @var \Magento\ObjectManager\ConfigCache
     */
    protected $_cache;

    /**
     * Class definitions
     *
     * @var \Magento\ObjectManager\Definition
     */
    protected $_definitions;

    /**
     * Current cache key
     *
     * @var string
     */
    protected $_currentCacheKey;

    /**
     * Interface preferences
     *
     * @var array
     */
    protected $_preferences = array();

    /**
     * Virtual types
     *
     * @var array
     */
    protected $_virtualTypes = array();

    /**
     * Instance arguments
     *
     * @var array
     */
    protected $_arguments = array();

    /**
     * Type shareability
     *
     * @var array
     */
    protected $_nonShared = array();

    /**
     * Plugin configuration
     *
     * @var array
     */
    protected $_plugins = array();

    /**
     * Merged plugin config
     *
     * @var array
     */
    protected $_mergedPlugins = array();

    /**
     * List of relations
     *
     * @var \Magento\ObjectManager\Relations
     */
    protected $_relations;

    /**
     * List of merged arguments
     *
     * @var array
     */
    protected $_mergedArguments;

    /**
     * @param \Magento\ObjectManager\Relations $relations
     * @param \Magento\ObjectManager\Definition $definitions
     */
    public function __construct(
        \Magento\ObjectManager\Relations $relations = null,
        \Magento\ObjectManager\Definition $definitions = null
    ) {
        $this->_relations = $relations ?: new \Magento\ObjectManager\Relations\Runtime();
        $this->_definitions = $definitions ?: new \Magento\ObjectManager\Definition\Runtime();
    }

    /**
     * Set class relations
     *
     * @param \Magento\ObjectManager\Relations $relations
     */
    public function setRelations(\Magento\ObjectManager\Relations $relations)
    {
        $this->_relations = $relations;
    }

    /**
     * Set cache instance
     *
     * @param \Magento\ObjectManager\ConfigCache $cache
     */
    public function setCache(\Magento\ObjectManager\ConfigCache $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @param array $arguments
     * @return array
     */
    public function getArguments($type, $arguments)
    {
        if (isset($this->_mergedArguments[$type]) && is_array($this->_mergedArguments[$type])) {
            $arguments = array_replace($this->_mergedArguments[$type], $arguments);
        }
        return $arguments;
    }

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return bool
     */
    public function isShared($type)
    {
        return !isset($this->_nonShared[$type]);
    }

    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     * @return mixed
     */
    public function getInstanceType($instanceName)
    {
        while (isset($this->_virtualTypes[$instanceName])) {
            $instanceName = $this->_virtualTypes[$instanceName];
        }
        return $instanceName;
    }

    /**
     * Retrieve preference for type
     *
     * @param string $type
     * @return string
     * @throws \LogicException
     */
    public function getPreference($type)
    {
        $preferencePath = array();
        while (isset($this->_preferences[$type])) {
            if (isset($preferencePath[$this->_preferences[$type]])) {
                throw new \LogicException(
                    'Circular type preference: ' . $type . ' relates to '
                    . $this->_preferences[$type] . ' and viceversa.'
                );
            }
            $type = $this->_preferences[$type];
            $preferencePath[$type] = 1;
        }
        return $type;
    }

    /**
     * Collect parent types configuration for requested type
     *
     * @param string $type
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _collectConfiguration($type)
    {
        if (!isset($this->_mergedArguments[$type])) {
            if (isset($this->_virtualTypes[$type])) {
                list($plugins, $arguments) = $this->_collectConfiguration($this->_virtualTypes[$type]);
            } else if ($this->_relations->has($type)) {
                $relations = $this->_relations->getParents($type);
                $plugins = array();
                $arguments = array();
                foreach ($relations as $relation) {
                    if ($relation) {
                        list($relationPlugins, $relationArguments) = $this->_collectConfiguration($relation);
                        if ($relationPlugins) {
                            $plugins = array_replace($plugins, $relationPlugins);
                        }
                        if ($relationArguments) {
                            $arguments = array_replace($arguments, $relationArguments);
                        }
                    }
                }
            } else {
                $plugins = array();
                $arguments = array();
            }

            if (isset($this->_plugins[$type])) {
                if ($plugins && count($plugins)) {
                    $plugins = array_replace_recursive($plugins, $this->_plugins[$type]);
                } else {
                    $plugins = $this->_plugins[$type];
                }
            }
            if (isset($this->_arguments[$type])) {
                if ($arguments && count($arguments)) {
                    $arguments = array_replace_recursive($arguments, $this->_arguments[$type]);
                } else {
                    $arguments = $this->_arguments[$type];
                }
            }
            if (!is_array($plugins) || !count($plugins)) {
                $plugins = false;
            } else {
                usort($plugins, array($this, '_sort'));
                $this->_mergedPlugins[$type] = $plugins;
            }
            $this->_mergedArguments[$type] = $arguments;
            return array($plugins, $arguments);
        }
        return array(
            isset($this->_mergedPlugins[$type]) ? $this->_mergedArguments[$type] : false,
            $this->_mergedArguments[$type]
        );
    }

    /**
     * Check whether type has configured plugins
     *
     * @param string $type
     * @return bool
     */
    public function hasPlugins($type)
    {
        if (!isset($this->_mergedArguments[$type])) {
            $this->_collectConfiguration($type);
        }
        return isset($this->_mergedPlugins[$type]);
    }

    /**
     * Retrieve list of plugins
     *
     * @param string $type
     * @return array
     */
    public function getPlugins($type)
    {
        return $this->_mergedPlugins[$type];
    }

    /**
     * Merge configuration
     *
     * @param array $configuration
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function _mergeConfiguration(array $configuration)
    {
        foreach ($configuration as $key => $curConfig) {
            switch ($key) {
                case 'preferences':
                    $this->_preferences = array_replace($this->_preferences, $curConfig);
                    break;

                default:
                    if (isset($curConfig['type'])) {
                        $this->_virtualTypes[$key] = $curConfig['type'];
                    }
                    if (isset($curConfig['parameters'])) {
                        if (!empty($this->_mergedArguments)) {
                            $this->_mergedArguments = array();
                        }
                        if (isset($this->_arguments[$key])) {
                            $this->_arguments[$key] = array_replace($this->_arguments[$key], $curConfig['parameters']);
                        } else {
                            $this->_arguments[$key] = $curConfig['parameters'];
                        }
                    }
                    if (isset($curConfig['shared'])) {
                        if (!$curConfig['shared']) {
                            $this->_nonShared[$key] = 1;
                        } else {
                            unset($this->_nonShared[$key]);
                        }
                    }
                    if (isset($curConfig['plugins'])) {
                        if (!empty($this->_mergedPlugins)) {
                            $this->_mergedPlugins = array();
                        }
                        if (isset($this->_plugins[$key])) {
                            $this->_plugins[$key] = array_replace($this->_plugins[$key], $curConfig['plugins']);
                        } else {
                            $this->_plugins[$key] = $curConfig['plugins'];
                        }
                    }
                    break;
            }
        }
    }

    /**
     * Sort items
     *
     * @param array $itemA
     * @param array $itemB
     * @return int
     */
    protected function _sort($itemA, $itemB)
    {
        if (isset($itemA['sortOrder'])) {
            if (isset($itemB['sortOrder'])) {
                return $itemA['sortOrder'] - $itemB['sortOrder'];
            }
            return $itemA['sortOrder'];
        } else if (isset($itemB['sortOrder'])) {
            return $itemB['sortOrder'];
        } else {
            return 1;
        }
    }

    /**
     * Extend configuration
     *
     * @param array $configuration
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function extend(array $configuration)
    {
        if ($this->_cache) {
            if (!$this->_currentCacheKey) {
                $this->_currentCacheKey = md5(serialize(array(
                    $this->_plugins, $this->_arguments, $this->_nonShared, $this->_preferences, $this->_virtualTypes
                )));
            }
            $key = md5($this->_currentCacheKey . serialize($configuration));
            $cached = $this->_cache->get($key);
            if ($cached) {
                list(
                    $this->_plugins, $this->_arguments, $this->_nonShared, $this->_preferences, $this->_virtualTypes,
                    $this->_mergedPlugins, $this->_mergedArguments
                ) = $cached;
            } else {
                $this->_mergeConfiguration($configuration);
                if (!$this->_mergedArguments || !$this->_mergedPlugins) {
                    foreach ($this->_definitions->getClasses() as $class) {
                        $this->_collectConfiguration($class);
                    }
                }
                $this->_cache->save(array(
                    $this->_plugins, $this->_arguments, $this->_nonShared, $this->_preferences, $this->_virtualTypes,
                    $this->_mergedPlugins, $this->_mergedArguments
                ), $key);
            }
            $this->_currentCacheKey = $key;
        } else {
            $this->_mergeConfiguration($configuration);
        }
    }
}
