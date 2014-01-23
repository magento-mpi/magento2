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

use Magento\ObjectManager;
use Magento\ObjectManager\Factory;

class FactoryDecorator implements Factory
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
        Factory $factory,
        Config $config,
        PluginList $pluginList,
        ObjectManager $objectManager
    ) {
        $this->_factory = $factory;
        $this->_pluginList = $pluginList;
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * Set object manager
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
        $this->_factory->setObjectManager($objectManager);
    }

    /**
     * Set application arguments
     *
     * @param array $arguments
     * @return void
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
     * @return object
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
