<?php
/**
 * Object manager factory decorator. Wraps intercepted objects by Interceptor instance
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Interception_FactoryDecorator implements Magento_ObjectManager_Factory
{
    /**
     * Configurable factory
     *
     * @var Magento_ObjectManager_Factory
     */
    protected $_factory;

    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Object manager config
     *
     * @var Magento_Interception_Config
     */
    protected $_config;

    /**
     * List of plugins configured for instance
     *
     * @var Magento_Interception_PluginList
     */
    protected $_pluginList;

    /**
     * @param Magento_ObjectManager_Factory $factory
     * @param Magento_Interception_Config $config
     * @param Magento_Interception_PluginList $pluginList
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_ObjectManager_Factory $factory,
        Magento_Interception_Config $config,
        Magento_Interception_PluginList $pluginList,
        Magento_ObjectManager $objectManager
    ) {
        $this->_factory = $factory;
        $this->_pluginList = $pluginList;
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * Set object manager
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function setObjectManager(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
        $this->_factory->setObjectManager($objectManager);
    }

    /**
     * Set application arguments
     *
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->_factory->setArguments($arguments);
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
            $interceptorClass = $this->_config->getInterceptorClassName($type);
            return new $interceptorClass(
                $this->_factory,
                $this->_objectManager,
                $type,
                $this->_pluginList,
                $arguments
            );
        }
        return $this->_factory->create($type, $arguments);
    }
}
