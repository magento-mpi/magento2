<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Interception_Config_Config extends Magento_Config_Data implements Magento_Interception_Config
{
    /**
     * @var Magento_ObjectManager_Config
     */
    protected $_omConfig;

    /**
     * @var Magento_Interception_CodeGenerator
     */
    protected $_codeGenerator;

    /**
     * @var Magento_Interception_Definition
     */
    protected $_definitions;

    /**
     * Inherited list of intercepted types
     *
     * @var array
     */
    protected $_intercepted = array();

    /**
     * @var array
     */
    protected $_interceptedRaw = null;

    /**
     * Default plugin list
     *
     * @var array
     */
    protected $_default = array();

    public function __construct(
        Magento_Config_ReaderInterface $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        Magento_ObjectManager_Relations $relations,
        Magento_ObjectManager_Config $omConfig,
        Magento_Interception_Definition $definitions,
        Magento_Interception_CodeGenerator $codeGenerator,
        $cacheId
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
        $this->_omConfig = $omConfig;
        $this->_relations = $relations;
        $this->_definitions = $definitions;
        $this->_codeGenerator = $codeGenerator;
    }

    protected function _inheritInterception($type)
    {
        if (!isset($this->_intercepted[$type])) {
            if (isset($this->_interceptedRaw[$type])) {
                $this->_intercepted[$type] = true;
                return true;
            }
            $realType = $this->_omConfig->getInstanceType($type);
            if ($type !== $realType) {
                if ($this->_inheritInterception($realType)) {
                    $this->_intercepted[$type] = true;
                    return true;
                }
            } else if ($this->_relations->has($type)) {
                $relations = $this->_relations->getParents($type);
                foreach ($relations as $relation) {
                    if ($relation && $this->_inheritInterception($relation)) {
                        $this->_intercepted[$type] = true;
                        return true;
                    }
                }
            }

            if (isset($this->_pluginsRaw[$type])) {
                $this->_intercepted[$type] = true;
                return true;
            }
        }
        $this->_intercepted[$type] = false;
        return false;
    }

    protected function _collectInterception($type)
    {
        if ($this->_interceptedRaw === null) {
            $data = $this->_cache->get('all', 'interceptedRaw');
            if ($data) {
                $this->_interceptedRaw = unserialize($data);
            } else {
                $config = array();
                foreach ($this->_configScope->getAllScopes() as $scope) {
                    $config = array_replace_recursive($config, $this->_reader->read($scope));
                }
                foreach ($config as $type) {
                    if (isset($type['plugins'])) {
                        $this->_interceptedRaw[$type['name']] = true;
                    }
                }
                $this->_cache->put(serialize($this->_interceptedRaw), 'all', 'interceptedRaw');
            }
        }
        return $this->_inheritInterception($type);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPlugins($type)
    {
        return isset($this->_intercepted[$type]) ? $this->_intercepted[$type] : $this->_collectInterception($type);
    }


    /**
     * Collect parent types configuration for requested type
     *
     * @param string $type
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _inheritPlugins($type)
    {
        if (!isset($this->_data['inherited'][$type])) {
            $realType = $this->_omConfig->getInstanceType($type);
            if ($realType !== $type) {
                $plugins = $this->_inheritPlugins($realType);
            } else if ($this->_relations->has($type)) {
                $relations = $this->_relations->getParents($type);
                $plugins = array();
                foreach ($relations as $relation) {
                    if ($relation) {
                        $relationPlugins = $this->_inheritPlugins($relation);
                        if ($relationPlugins) {
                            $plugins = array_replace_recursive($plugins, $relationPlugins);
                        }
                    }
                }
            } else {
                $plugins = array();
            }

            if (isset($this->_data[$type])) {
                if (!$plugins) {
                    $plugins = $this->_data[$type];
                } else {
                    $plugins = array_replace_recursive($plugins, $this->_data[$type]);
                }
            }
            usort($plugins, array($this, '_sort'));
            $this->_data['inherited'][$type] = $plugins;
            foreach ($plugins as $plugin) {
                foreach ($this->_definitions->getMethodList($plugin) as $method) {
                    foreach ($method as $scenario) {
                        $this->_data['processed'][$type][$method][$scenario][] = $plugin;
                    }
                }
            }
            return $plugins;
        }
        return $this->_data['inherited'][$type];
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
     * {@inheritdoc}
     */
    public function getPlugins($type, $method, $scenario)
    {
        $this->_loadScopedData();
        if (!isset($this->_data['processed'][$this->_omConfig->getInstanceType($type)][$method][$scenario])) {
            $this->_inheritPlugins($type);
        }
        return $this->_data['processed'][$this->_omConfig->getInstanceType($type)][$method][$scenario];
    }

    /**
     * {@inheritdoc}
     */
    public function getInterceptorClassName($type)
    {
        $className = $this->_omConfig->getInstanceType($type) . '_Interceptor';
        if ($this->_codeGenerator && !class_exists($className)) {
            $this->_codeGenerator->generate($className);
        }
        return $className;
    }
}
