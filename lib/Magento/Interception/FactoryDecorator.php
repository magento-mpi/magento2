<?php
/**
 * Object manager factory decorator. Wraps intercepted objects by Interceptor instance
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception;

class FactoryDecorator implements \Magento\ObjectManager\Factory
{
    /**
     * Configurable factory
     *
     * @var \Magento\ObjectManager\Factory
     */
    protected $_factory;

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Object manager config
     *
     * @var \Magento\Interception\Config
     */
    protected $_config;

    /**
     * List of plugins configured for instance
     *
     * @var \Magento\Interception\PluginList
     */
    protected $_pluginList;

    /**
     * @param \Magento\ObjectManager\Factory $factory
     * @param \Magento\Interception\Config $config
     * @param \Magento\Interception\PluginList $pluginList
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\ObjectManager\Factory $factory,
        \Magento\Interception\Config $config,
        \Magento\Interception\PluginList $pluginList,
        \Magento\ObjectManager $objectManager
    ) {
        $this->_factory = $factory;
        $this->_pluginList = $pluginList;
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * Set object manager
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function setObjectManager(\Magento\ObjectManager $objectManager)
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
