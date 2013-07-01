<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Factory_InterceptionDecorator
{
    /**
     * List of virtual types
     *
     * @var array
     */
    protected $_virtualTypes = array();

    /**
     * List of configured interceptors
     *
     * @var array
     */
    protected $_plugins = array();

    /**
     * Configurable factory
     *
     * @var Magento_ObjectManager_Factory
     */
    protected $_factory;

    /**
     * List of plugin definitions
     *
     * @var Magento_ObjectManager_Interception_Definition
     */
    protected $_definitions;

    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager_Factory $factory
     * @param Magento_ObjectManager_ObjectManager $objectManager
     * @param Magento_ObjectManager_Interception_Definition $definitions
     */
    public function __construct(
        Magento_ObjectManager_Factory $factory,
        Magento_ObjectManager_ObjectManager $objectManager,
        Magento_ObjectManager_Interception_Definition $definitions = null
    ) {
        $this->_factory = $factory;
        $this->_objectManager = $objectManager;
        $this->_definitions = $definitions ?: new Magento_ObjectManager_Interception_Definition_Runtime();
    }

    /**
     * Create interceptor instance
     *
     * @param string $type
     * @param array $arguments
     * @return mixed
     */
    protected function _createInterceptor($type, $arguments = array())
    {
        $interceptorClass = $this->_resolveInstanceType($type) . '_Interceptor';
        $config = array();
        usort($this->_plugins[$type], array($this, '_sort'));
        foreach ($this->_plugins[$type] as $interceptor) {
            $pluginMethods = $this->_definitions->getMethodList($this->_resolveInstanceType($interceptor['instance']));
            foreach ($pluginMethods as $method) {
                if (isset($config[$method])) {
                    $config[$method][] = $interceptor['instance'];
                } else {
                    $config[$method] = array($interceptor['instance']);
                }
            }
        }
        return new $interceptorClass($this->_factory, $this->_objectManager, $type, $config, $arguments);
    }

    /**
     * Sorting function used to sort interceptors
     *
     * @param string $interceptorA
     * @param string $interceptorB
     * @return int
     */
    protected function _sort($interceptorA, $interceptorB)
    {
        if (isset($interceptorA['sortOrder'])) {
            if (isset($interceptorB['sortOrder'])) {
                return $interceptorA['sortOrder'] - $interceptorB['sortOrder'];
            }
            return $interceptorA['sortOrder'];
        } else if (isset($interceptorB['sortOrder'])) {
            return $interceptorB['sortOrder'];
        } else {
            return 1;
        }
    }

    /**
     * Resolve instance name
     *
     * @param string $instanceName
     * @return string
     */
    protected function _resolveInstanceType($instanceName)
    {
        while (isset($this->_virtualTypes[$instanceName])) {
            $instanceName = $this->_virtualTypes[$instanceName];
        }
        return $instanceName;
    }

    /**
     * Create instance of requested type with requested arguments
     *
     * @param string $type
     * @param array $arguments
     * @return mixed
     */
    public function create($type, array $arguments = array())
    {
        if (isset($this->_plugins[$type])) {
            return $this->_createInterceptor($type, $arguments);
        }
        return $this->_factory->create($type, $arguments);
    }

    /**
     * Configure plugins
     *
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        foreach ($configuration as $key => $curConfig) {
            if (isset($curConfig['type'])) {
                $this->_virtualTypes[$key] = $curConfig['type'];
            }
            if (isset($curConfig['plugins'])) {
                if (isset($this->_plugins[$key])) {
                    $this->_plugins[$key] = array_replace($this->_plugins[$key], $curConfig['plugins']);
                } else {
                    $this->_plugins[$key] = $curConfig['plugins'];
                }
            }
        }
        $this->_factory->configure($configuration);
    }
}
