<?php
/**
 * Interception config. Responsible for providing list of plugins configured for instance
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Interception_Config_Config implements Magento_Interception_Config
{
    /**
     * Type configuration
     *
     * @var Magento_ObjectManager_Config
     */
    protected $_omConfig;

    /**
     * Class relations info
     *
     * @var Magento_ObjectManager_Relations
     */
    protected $_relations;

    /**
     * Interceptor generator
     *
     * @var Magento_Interception_CodeGenerator
     */
    protected $_codeGenerator;

    /**
     * List of interceptable classes
     *
     * @var Magento_ObjectManager_Definition
     */
    protected $_classDefinitions;

    /**
     * Cache
     *
     * @var Magento_Cache_FrontendInterface
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
     * @var Magento_Config_ReaderInterface
     */
    protected $_reader;

    /**
     * Configuration scope resolver
     *
     * @var Magento_Config_ScopeInterface
     */
    protected $_configScope;

    /**
     * Inherited list of intercepted types
     *
     * @var array
     */
    protected $_intercepted = array();

    /**
     * @param Magento_Config_ReaderInterface $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Cache_FrontendInterface $cache
     * @param Magento_ObjectManager_Relations $relations
     * @param Magento_ObjectManager_Config $omConfig
     * @param Magento_ObjectManager_Definition_Compiled $classDefinitions
     * @param Magento_Interception_CodeGenerator $codeGenerator
     * @param string $cacheId
     */
    public function __construct(
        Magento_Config_ReaderInterface $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Cache_FrontendInterface $cache,
        Magento_ObjectManager_Relations $relations,
        Magento_ObjectManager_Config $omConfig,
        Magento_ObjectManager_Definition_Compiled $classDefinitions = null,
        Magento_Interception_CodeGenerator $codeGenerator = null,
        $cacheId = 'interception'
    ) {
        $this->_omConfig = $omConfig;
        $this->_relations = $relations;
        $this->_codeGenerator = $codeGenerator;
        $this->_classDefinitions = $classDefinitions;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
        $this->_reader = $reader;
        $this->_configScope = $configScope;

        $intercepted = $this->_cache->load($this->_cacheId);
        if ($intercepted !== false) {
            $this->_intercepted = unserialize($intercepted);
        } else {
            $config = array();
            foreach ($this->_configScope->getAllScopes() as $scope) {
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
            } else if ($this->_relations->has($type)) {
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
        $className = $this->_omConfig->getInstanceType($type) . '_Interceptor';
        if ($this->_codeGenerator && !class_exists($className)) {
            $this->_codeGenerator->generate($className);
        }
        return $className;
    }
}
