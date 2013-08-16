<?php
/**
 * Interception config. Responsible for providing list of plugins configured for instance
 *
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
     * Interceptor generator
     *
     * @var Magento_Interception_CodeGenerator
     */
    protected $_codeGenerator;

    /**
     * @var Magento_Interception_Definition
     */
    protected $_definitions;

    /**
     * @var Magento_ObjectManager_Definition
     */
    protected $_classDefinitions;

    /**
     * Scope inheritance scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

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
        Magento_Interception_CodeGenerator $codeGenerator,
        Magento_ObjectManager_Relations $relations,
        Magento_ObjectManager_Config $omConfig,
        Magento_Interception_Definition $definitions,
        Magento_Interception_CodeGenerator $codeGenerator = null,
        Magento_ObjectManager_Definition_Compiled $classDefinitions = null,
        $cacheId
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
        $this->_omConfig = $omConfig;
        $this->_relations = $relations;
        $this->_definitions = $definitions;
        $this->_codeGenerator = $codeGenerator;
        $this->_classDefinitions = $classDefinitions;
        $this->_cacheId = $cacheId;
        if ($classDefinitions) {
            $data = $this->_cache->get('all', $this->_cacheId . 'all');
            if (!$data) {
                foreach ($classDefinitions->getClasses() as $class) {
                    $this->hasPlugins($class);
                }
                $this->_cache->put($this->_intercepted, 'all', $this->_cacheId . 'all');
            } else {
                $this->_intercepted = unserialize($data);
            }
        }
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
            $data = $this->_cache->get('all', $this->_cacheId . 'Raw');
            if ($data) {
                $this->_interceptedRaw = unserialize($data);
            } else {
                $config = array();
                foreach ($this->_configScope->getAllScopes() as $scope) {
                    $config = array_replace_recursive($config, $this->_reader->read($scope));
                }
                foreach ($config as $typeName => $typeConfig) {
                    if (!empty($typeConfig['plugins'])) {
                        $this->_interceptedRaw[$typeName] = true;
                    }
                }
                $this->_cache->put(serialize($this->_interceptedRaw), 'all', $this->_cacheId . 'Raw');
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

            if (isset($this->_data[$type]['plugins'])) {
                if (!$plugins) {
                    $plugins = $this->_data[$type]['plugins'];
                } else {
                    $plugins = array_replace_recursive($plugins, $this->_data[$type]['plugins']);
                }
            }
            uasort($plugins, array($this, '_sort'));
            $this->_data['inherited'][$type] = $plugins;
            foreach ($plugins as $plugin) {
                // skip disabled plugins
                if (isset($plugin['disabled']) && $plugin['disabled']) {
                    continue;
                }
                $pluginType = $this->_omConfig->getInstanceType($plugin['instance']);
                foreach ($this->_definitions->getMethodList($pluginType) as $pluginMethod) {
                    $this->_data['processed'][$type][$pluginMethod][] = $plugin['instance'];
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
        $pluginMethodName = $scenario . ucfirst($method);
        $realType = $this->_omConfig->getInstanceType($type);
        if (!isset($this->_data['processed'][$realType])) {
            $this->_inheritPlugins($type);
        }
        return isset($this->_data['processed'][$realType][$pluginMethodName])
            ? $this->_data['processed'][$realType][$pluginMethodName]
            : array();
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
