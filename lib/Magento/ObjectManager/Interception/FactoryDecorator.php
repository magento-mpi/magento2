<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Interception;

class FactoryDecorator implements \Magento\ObjectManager\Factory
{
    /**
     * Configurable factory
     *
     * @var \Magento\ObjectManager\Factory
     */
    protected $_factory;

    /**
     * List of plugin definitions
     *
     * @var \Magento\ObjectManager\Interception\Definition
     */
    protected $_definitions;

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Object manager config
     *
     * @var \Magento\ObjectManager\Config
     */
    protected $_config;

    /**
     * Interceptor class builder
     *
     * @var \Magento\ObjectManager\Interception\ClassBuilder
     */
    protected $_classBuilder;

    /**
     * @param \Magento\ObjectManager\Factory $factory
     * @param \Magento\ObjectManager\Config $config
     * @param \Magento\ObjectManager\ObjectManager $objectManager
     * @param \Magento\ObjectManager\Interception\Definition $definitions
     * @param \Magento\ObjectManager\Interception\ClassBuilder $classBuilder
     */
    public function __construct(
        \Magento\ObjectManager\Factory $factory,
        \Magento\ObjectManager\Config $config,
        \Magento\ObjectManager\ObjectManager $objectManager = null,
        \Magento\ObjectManager\Interception\Definition $definitions = null,
        \Magento\ObjectManager\Interception\ClassBuilder $classBuilder = null
    ) {
        $this->_factory = $factory;
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_definitions = $definitions ?: new \Magento\ObjectManager\Interception\Definition\Runtime();
        $this->_classBuilder = $classBuilder ?: new \Magento\ObjectManager\Interception\ClassBuilder\General();
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
     * Create instance of requested type with requested arguments
     *
     * @param string $type
     * @param array $arguments
     * @return mixed
     */
    public function create($type, array $arguments = array())
    {
        if ($this->_config->hasPlugins($type)) {
            $interceptorClass = $this->_classBuilder
                ->composeInterceptorClassName($this->_config->getInstanceType($type));
            $config = array();
            foreach ($this->_config->getPlugins($type) as $plugin) {
                if (isset($plugin['disabled']) && $plugin['disabled']) {
                    continue;
                }
                $pluginMethods = $this->_definitions->getMethodList(
                    $this->_config->getInstanceType($plugin['instance'])
                );
                foreach ($pluginMethods as $method) {
                    if (isset($config[$method])) {
                        $config[$method][] = $plugin['instance'];
                    } else {
                        $config[$method] = array($plugin['instance']);
                    }
                }
            }
            return new $interceptorClass($this->_factory, $this->_objectManager, $type, $config, $arguments);
        }
        return $this->_factory->create($type, $arguments);
    }
}
