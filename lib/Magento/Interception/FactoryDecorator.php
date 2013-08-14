<?php
/**
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
     * @param Magento_ObjectManager_Factory $factory
     * @param Magento_Interception_Config $config
     * @param Magento_ObjectManager_ObjectManager $objectManager
     */
    public function __construct(
        Magento_ObjectManager_Factory $factory,
        Magento_Interception_Config $config,
        Magento_ObjectManager_ObjectManager $objectManager = null
    ) {
        $this->_factory = $factory;
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
                $this->_objectManager->get('Magento_Interception_PluginList'),
                $arguments
            );
        }
        return $this->_factory->create($type, $arguments);
    }
}
