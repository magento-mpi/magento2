<?php
/**
 * Plugin configuration storage. Provides list of plugins configured for type.
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Interception_PluginList_PluginList
    extends Magento_Config_Data_Scoped
    implements Magento_Interception_PluginList
{
    /**
     * Type config
     *
     * @var Magento_ObjectManager_Config
     */
    protected $_omConfig;

    /**
     * Class relations information provider
     *
     * @var Magento_ObjectManager_Relations
     */
    protected $_relations;

    /**
     * List of interception methods per plugin
     *
     * @var Magento_Interception_Definition
     */
    protected $_definitions;

    /**
     * List of interceptable application classes
     *
     * @var Magento_ObjectManager_Definition_Compiled
     */
    protected $_classDefinitions;

    /**
     * Scope inheritance scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param Magento_Config_ReaderInterface $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param Magento_ObjectManager_Relations $relations
     * @param Magento_ObjectManager_Config $omConfig
     * @param Magento_Interception_Definition $definitions
     * @param array $scopePriorityScheme
     * @param Magento_ObjectManager_Definition_Compiled $classDefinitions
     * @param string $cacheId
     */
    public function __construct(
        Magento_Config_ReaderInterface $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        Magento_ObjectManager_Relations $relations,
        Magento_ObjectManager_Config $omConfig,
        Magento_Interception_Definition $definitions,
        array $scopePriorityScheme,
        Magento_ObjectManager_Definition_Compiled $classDefinitions = null,
        $cacheId = 'plugins'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
        $this->_omConfig = $omConfig;
        $this->_relations = $relations;
        $this->_definitions = $definitions;
        $this->_classDefinitions = $classDefinitions;
        $this->_scopePriorityScheme = $scopePriorityScheme;
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
            if (count($plugins)) {
                $this->_data['inherited'][$type] = $plugins;
                foreach ($plugins as $key => $plugin) {
                    // skip disabled plugins
                    if (isset($plugin['disabled']) && $plugin['disabled']) {
                        unset($plugins[$key]);
                        continue;
                    }
                    $pluginType = $this->_omConfig->getInstanceType($plugin['instance']);
                    foreach ($this->_definitions->getMethodList($pluginType) as $pluginMethod) {
                        $this->_data['processed'][$type][$pluginMethod][] = $plugin['instance'];
                    }
                }
            } else {
                $this->_data['inherited'][$type] = null;
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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getPlugins($type, $method, $scenario)
    {
        $this->_loadScopedData();
        $pluginMethodName = $scenario . ucfirst($method);
        $realType = $this->_omConfig->getInstanceType($type);
        if (!isset($this->_data['inherited'][$realType])) {
            $this->_inheritPlugins($type);
        }
        return isset($this->_data['processed'][$realType][$pluginMethodName])
            ? $this->_data['processed'][$realType][$pluginMethodName]
            : array();
    }

    /**
     * Load configuration for current scope
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _loadScopedData()
    {
        $scope = $this->_configScope->getCurrentScope();
        if (false == isset($this->_loadedScopes[$scope])) {
            if (false == in_array($scope, $this->_scopePriorityScheme)) {
                $this->_scopePriorityScheme[] = $scope;
            }
            $cacheScope = implode('|', $this->_scopePriorityScheme);
            $data = $this->_cache->get($cacheScope, $this->_cacheId);
            if ($data) {
                $this->_data = unserialize($data);
                foreach ($this->_scopePriorityScheme as $scope) {
                    $this->_loadedScopes[$scope] = true;
                }
            } else {
                foreach ($this->_scopePriorityScheme as $scopeCode) {
                    if (false == isset($this->_loadedScopes[$scopeCode])) {
                        $data = $this->_reader->read($scopeCode);
                        if (!count($data)) {
                            continue;
                        }
                        unset($this->_data['inherited']);
                        unset($this->_data['processed']);
                        $this->merge($data);
                        $this->_loadedScopes[$scopeCode] = true;
                    }
                    if ($scopeCode == $scope) {
                        break;
                    }
                }
                if ($this->_classDefinitions) {
                    foreach ($this->_classDefinitions->getClasses() as $class) {
                        $this->_inheritPlugins($class);
                    }
                }
                $this->_cache->put(serialize($this->_data), $cacheScope, $this->_cacheId);
            }
        }
    }
}
