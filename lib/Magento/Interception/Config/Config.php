<?php
/**
 * Interception config. Responsible for providing list of plugins configured for instance
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Config;

class Config implements \Magento\Interception\Config
{
    /**
     * Type configuration
     *
     * @var \Magento\Framework\ObjectManager\Config
     */
    protected $_omConfig;

    /**
     * Class relations info
     *
     * @var \Magento\Framework\ObjectManager\Relations
     */
    protected $_relations;

    /**
     * List of interceptable classes
     *
     * @var \Magento\Framework\ObjectManager\Definition
     */
    protected $_classDefinitions;

    /**
     * Cache
     *
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cache;

    /**
     * Cache identifier
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Configuration reader
     *
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_reader;

    /**
     * Inherited list of intercepted types
     *
     * @var array
     */
    protected $_intercepted = array();

    /**
     * List of class types that can not be pluginized
     *
     * @var array
     */
    protected $_serviceClassTypes = array('Proxy', 'Interceptor');

    /**
     * @param \Magento\Framework\Config\ReaderInterface $reader
     * @param \Magento\Framework\Config\ScopeListInterface $scopeList
     * @param \Magento\Cache\FrontendInterface $cache
     * @param \Magento\Framework\ObjectManager\Relations $relations
     * @param \Magento\Interception\ObjectManager\Config $omConfig
     * @param \Magento\Framework\ObjectManager\Definition $classDefinitions
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $reader,
        \Magento\Framework\Config\ScopeListInterface $scopeList,
        \Magento\Cache\FrontendInterface $cache,
        \Magento\Framework\ObjectManager\Relations $relations,
        \Magento\Interception\ObjectManager\Config $omConfig,
        \Magento\Framework\ObjectManager\Definition $classDefinitions,
        $cacheId = 'interception'
    ) {
        $this->_omConfig = $omConfig;
        $this->_relations = $relations;
        $this->_classDefinitions = $classDefinitions;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
        $this->_reader = $reader;

        $intercepted = $this->_cache->load($this->_cacheId);
        if ($intercepted !== false) {
            $this->_intercepted = unserialize($intercepted);
        } else {
            $config = array();
            foreach ($scopeList->getAllScopes() as $scope) {
                $config = array_replace_recursive($config, $this->_reader->read($scope));
            }
            unset($config['preferences']);
            foreach ($config as $typeName => $typeConfig) {
                if (!empty($typeConfig['plugins'])) {
                    $this->_intercepted[ltrim($typeName, '\\')] = true;
                }
            }
            foreach ($config as $typeName => $typeConfig) {
                $this->hasPlugins(ltrim($typeName, '\\'));
            }
            foreach ($classDefinitions->getClasses() as $class) {
                $this->hasPlugins($class);
            }
            $this->_cache->save(serialize($this->_intercepted), $this->_cacheId);
        }
    }

    /**
     * Process interception inheritance
     *
     * @param string $type
     * @return bool
     */
    protected function _inheritInterception($type)
    {
        if (!isset($this->_intercepted[$type])) {
            $realType = $this->_omConfig->getOriginalInstanceType($type);
            if ($type !== $realType) {
                if ($this->_inheritInterception($realType)) {
                    $this->_intercepted[$type] = true;
                    return true;
                }
            } else {
                $parts = explode('\\', $type);
                if (!in_array(end($parts), $this->_serviceClassTypes) && $this->_relations->has($type)) {
                    $relations = $this->_relations->getParents($type);
                    foreach ($relations as $relation) {
                        if ($relation && $this->_inheritInterception($relation)) {
                            $this->_intercepted[$type] = true;
                            return true;
                        }
                    }
                }
            }
            $this->_intercepted[$type] = false;
        }
        return $this->_intercepted[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function hasPlugins($type)
    {
        return isset($this->_intercepted[$type]) ? $this->_intercepted[$type] : $this->_inheritInterception($type);
    }
}
