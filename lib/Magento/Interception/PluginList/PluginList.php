<?php
/**
 * Plugin configuration storage. Provides list of plugins configured for type.
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\PluginList;

use Magento\Config\ReaderInterface;
use Magento\Config\ScopeInterface;
use Magento\Config\CacheInterface;
use Magento\Config\Data\Scoped;
use Magento\Interception\Definition;
use Magento\Interception\PluginList as InterceptionPluginList;
use Magento\Interception\ObjectManager\Config;
use Magento\ObjectManager\Relations;
use Magento\ObjectManager\Definition as ClassDefinitions;
use Magento\ObjectManager;
use Zend\Soap\Exception\InvalidArgumentException;

class PluginList extends Scoped implements InterceptionPluginList
{
    /**
     * Type config
     *
     * @var Config
     */
    protected $_omConfig;

    /**
     * Class relations information provider
     *
     * @var Relations
     */
    protected $_relations;

    /**
     * List of interception methods per plugin
     *
     * @var Definition
     */
    protected $_definitions;

    /**
     * List of interceptable application classes
     *
     * @var ClassDefinitions
     */
    protected $_classDefinitions;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_pluginInstances = array();

    /**
     * @param ReaderInterface $reader
     * @param ScopeInterface $configScope
     * @param CacheInterface $cache
     * @param Relations $relations
     * @param Config $omConfig
     * @param Definition $definitions
     * @param ObjectManager $objectManager
     * @param ClassDefinitions $classDefinitions
     * @param array $scopePriorityScheme
     * @param string $cacheId
     */
    public function __construct(
        ReaderInterface $reader,
        ScopeInterface $configScope,
        CacheInterface $cache,
        Relations $relations,
        Config $omConfig,
        Definition $definitions,
        ObjectManager $objectManager,
        ClassDefinitions $classDefinitions,
        array $scopePriorityScheme = array('global'),
        $cacheId = 'plugins'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
        $this->_omConfig = $omConfig;
        $this->_relations = $relations;
        $this->_definitions = $definitions;
        $this->_classDefinitions = $classDefinitions;
        $this->_scopePriorityScheme = $scopePriorityScheme;
        $this->_objectManager = $objectManager;
    }

    /**
     * Collect parent types configuration for requested type
     *
     * @param string $type
     * @return array
     * @throws InvalidArgumentException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _inheritPlugins($type)
    {
        if (!array_key_exists($type, $this->_data['inherited'])) {
            $realType = $this->_omConfig->getOriginalInstanceType($type);

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
            $this->_data['inherited'][$type] = null;
            if (is_array($plugins) && count($plugins)) {
                uasort($plugins, array($this, '_sort'));
                $this->_data['inherited'][$type] = $plugins;
                $lastPerMethod = array();
                foreach ($plugins as $key => $plugin) {
                    // skip disabled plugins
                    if (isset($plugin['disabled']) && $plugin['disabled']) {
                        unset($plugins[$key]);
                        continue;
                    }
                    $pluginType = $this->_omConfig->getOriginalInstanceType($plugin['instance']);
                    if (!class_exists($pluginType)) {
                        throw new InvalidArgumentException('Plugin class ' . $pluginType . ' doesn\'t exist');
                    }
                    foreach ($this->_definitions->getMethodList($pluginType) as $pluginMethod => $methodTypes) {
                        $current = isset($lastPerMethod[$pluginMethod]) ? $lastPerMethod[$pluginMethod] : '__self';
                        $currentKey = $type . '_'. $pluginMethod . '_' . $current;
                        if ($methodTypes & Definition::LISTENER_AROUND) {
                            $this->_data['processed'][$currentKey][Definition::LISTENER_AROUND] = $key;
                            $lastPerMethod[$pluginMethod] = $key;
                        }
                        if ($methodTypes & Definition::LISTENER_BEFORE) {
                            $this->_data['processed'][$currentKey][Definition::LISTENER_BEFORE][] = $key;
                        }
                        if ($methodTypes & Definition::LISTENER_AFTER) {
                            $this->_data['processed'][$currentKey][Definition::LISTENER_AFTER][] = $key;
                        }
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
     * Retrieve plugin Instance
     *
     * @param string $type
     * @param string $code
     * @return mixed
     */
    public function getPlugin($type, $code)
    {
        if (!isset($this->_pluginInstances[$type][$code])) {
            $this->_pluginInstances[$type][$code] = $this->_objectManager->get(
                $this->_data['inherited'][$type][$code]['instance']
            );
        }
        return $this->_pluginInstances[$type][$code];
    }

    /**
     * Retrieve next plugins in chain
     *
     * @param string $type
     * @param string $method
     * @param string $code
     * @return array
     */
    public function getNext($type, $method, $code = '__self')
    {
        $this->_loadScopedData();
        if (!array_key_exists($type, $this->_data['inherited'])) {
            $this->_inheritPlugins($type);
        }
        $key = $type . '_' . lcfirst($method) . '_' . $code;
        return isset($this->_data['processed'][$key]) ? $this->_data['processed'][$key] : null;
    }

    /**
     * Load configuration for current scope
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _loadScopedData()
    {
        $scope = $this->_configScope->getCurrentScope();
        if (false == isset($this->_loadedScopes[$scope])) {
            if (false == in_array($scope, $this->_scopePriorityScheme)) {
                $this->_scopePriorityScheme[] = $scope;
            }
            $cacheId = implode('|', $this->_scopePriorityScheme) . "|" . $this->_cacheId;
            $data = $this->_cache->load($cacheId);
            if ($data) {
                $this->_data = unserialize($data);
                foreach ($this->_scopePriorityScheme as $scope) {
                    $this->_loadedScopes[$scope] = true;
                }
            } else {
                $virtualTypes = array();
                foreach ($this->_scopePriorityScheme as $scopeCode) {
                    if (false == isset($this->_loadedScopes[$scopeCode])) {
                        $data = $this->_reader->read($scopeCode);
                        unset($data['preferences']);
                        if (!count($data)) {
                            continue;
                        }
                        $this->_data['inherited'] = array();
                        $this->_data['processed'] = array();
                        $this->merge($data);
                        $this->_loadedScopes[$scopeCode] = true;
                        foreach ($data as $class => $config) {
                            if (isset($config['type'])) {
                                $virtualTypes[] = $class;
                            }
                        }
                    }
                    if ($scopeCode == $scope) {
                        break;
                    }
                }
                foreach ($virtualTypes as $class) {
                    $this->_inheritPlugins($class);
                }
                foreach ($this->_classDefinitions->getClasses() as $class) {
                    $this->_inheritPlugins($class);
                }
                $this->_cache->save(serialize($this->_data), $cacheId);
            }
            $this->_pluginInstances = array();
        }
    }
}
