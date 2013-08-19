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
     * Default plugin list
     *
     * @var array
     */
    protected $_default = array();

    /**
     * @param Magento_Config_ReaderInterface $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param Magento_ObjectManager_Relations $relations
     * @param Magento_ObjectManager_Config $omConfig
     * @param Magento_ObjectManager_Definition_Compiled $classDefinitions
     * @param Magento_Interception_CodeGenerator $codeGenerator
     * @param string $cacheId
     */
    public function __construct(
        Magento_Config_ReaderInterface $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        Magento_ObjectManager_Relations $relations,
        Magento_ObjectManager_Config $omConfig,
        Magento_ObjectManager_Definition_Compiled $classDefinitions = null,
        Magento_Interception_CodeGenerator $codeGenerator = null,
        $cacheId
    ) {
        $this->_omConfig = $omConfig;
        $this->_relations = $relations;
        $this->_codeGenerator = $codeGenerator;
        $this->_classDefinitions = $classDefinitions;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
        $this->_reader = $reader;
        $this->_configScope = $configScope;

        $intercepted = $this->_cache->get('all', $this->_cacheId);
        if ($intercepted !== false) {
            $this->_intercepted = $intercepted;
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
            $this->_cache->put($this->_intercepted, 'all', $this->_cacheId);
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
            if (isset($this->_intercepted[$type])) {
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
