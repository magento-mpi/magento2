<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Interception_FactoryDecorator
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
     * Object manager config
     *
     * @var Magento_ObjectManager_Config
     */
    protected $_config;

    /**
     * @param Magento_ObjectManager_Factory $factory
     * @param Magento_ObjectManager_ObjectManager $objectManager
     * @param Magento_ObjectManager_Config $config
     * @param Magento_ObjectManager_Interception_Definition $definitions
     */
    public function __construct(
        Magento_ObjectManager_Factory $factory,
        Magento_ObjectManager_ObjectManager $objectManager,
        Magento_ObjectManager_Config $config,
        Magento_ObjectManager_Interception_Definition $definitions = null
    ) {
        $this->_factory = $factory;
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_definitions = $definitions ?: new Magento_ObjectManager_Interception_Definition_Runtime();
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
        if ($this->_config->hasPlugins($type)) {
            $interceptorClass = $this->_config->getInstanceType($type) . '_Interceptor';
            $config = array();
            foreach ($this->_config->getPlugins($type) as $interceptor) {
                $pluginMethods = $this->_definitions->getMethodList(
                    $this->_config->getInstanceType($interceptor['instance'])
                );
                foreach ($pluginMethods as $method) {
                    if (isset($config[$method])) {
                        $config[$method][] = $interceptor['instance'];
                    } else {
                        $config[$method] = array($interceptor['instance']);
                    }
                }
            }
            return new $interceptorClass(
                $this->_factory,
                $this->_objectManager,
                $type,
                $config,
                $arguments
            );
        }
        return $this->_factory->create($type, $arguments);
    }
}
