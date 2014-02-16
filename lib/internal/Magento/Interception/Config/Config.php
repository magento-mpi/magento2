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
     * @var \Magento\ObjectManager\Config
     */
    protected $_omConfig;

    /**
     * Class relations info
     *
     * @var \Magento\ObjectManager\Relations
     */
    protected $_relations;

    /**
     * Interceptor generator
     *
     * @var \Magento\Interception\CodeGenerator
     */
    protected $_codeGenerator;

    /**
     * List of interceptable classes
     *
     * @var \Magento\ObjectManager\Definition
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
     * @var \Magento\Config\ReaderInterface
     */
    protected $_reader;

    /**
     * Inherited list of intercepted types
     *
     * @var array
     */
    protected $_intercepted = array();

    /**
     * @param \Magento\Config\ReaderInterface $reader
     * @param \Magento\Config\ScopeListInterface $scopeList
     * @param \Magento\Cache\FrontendInterface $cache
     * @param \Magento\ObjectManager\Relations $relations
     * @param \Magento\ObjectManager\Config $omConfig
     * @param \Magento\ObjectManager\Definition\Compiled $classDefinitions
     * @param \Magento\Interception\CodeGenerator $codeGenerator
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Config\ReaderInterface $reader,
        \Magento\Config\ScopeListInterface $scopeList,
        \Magento\Cache\FrontendInterface $cache,
        \Magento\ObjectManager\Relations $relations,
        \Magento\ObjectManager\Config $omConfig,
        \Magento\ObjectManager\Definition\Compiled $classDefinitions = null,
        \Magento\Interception\CodeGenerator $codeGenerator = null,
        $cacheId = 'interception'
    ) {
        $this->_omConfig = $omConfig;
        $this->_relations = $relations;
        $this->_codeGenerator = $codeGenerator;
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
            foreach ($config as $typeName => $typeConfig) {
                if (!empty($typeConfig['plugins'])) {
                    $this->_intercepted[$typeName] = true;
                }
            }
            if ($classDefinitions) {
                foreach ($classDefinitions->getClasses() as $class) {
                    $this->hasPlugins($class);
                }
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
            $realType = $this->_omConfig->getInstanceType($type);
            if ($type !== $realType) {
                if ($this->_inheritInterception($realType)) {
                    $this->_intercepted[$type] = true;
                    return true;
                }
            } else if (substr($type, -5) != 'Proxy' && $this->_relations->has($type)) {
                $relations = $this->_relations->getParents($type);
                foreach ($relations as $relation) {
                    if ($relation && $this->_inheritInterception($relation)) {
                        $this->_intercepted[$type] = true;
                        return true;
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

    /**
     * {@inheritdoc}
     */
    public function getInterceptorClassName($type)
    {
        $className = $this->_omConfig->getInstanceType($type) . '\Interceptor';
        if ($this->_codeGenerator && !class_exists($className)) {
            $this->_codeGenerator->generate($className);
        }
        return $className;
    }
}
