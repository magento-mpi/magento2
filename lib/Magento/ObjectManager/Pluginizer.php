<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Pluginizer implements Magento_ObjectManager
{
    /**
     * Config
     *
     * @var Magento_ObjectManager_Config
     */
    protected $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_plugins = array();

    /**
     * @param Magento_ObjectManager_Config $config
     */
    public function __construct(Magento_ObjectManager_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Create interceptor instance
     *
     * @param string $type
     * @param array $arguments
     * @param bool $isShared
     * @return mixed
     */
    public function _createInterceptor($type, $arguments = array(), $isShared = true)
    {
        $interceptorClass = $type . '_Interceptor';
        return new $interceptorClass(
            $this->_objectManager, $this->_config->getPluginList($type), $arguments, $isShared
        );
    }

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function setObjectManager(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new object instance
     *
     * @param string $type
     * @param array $arguments
     * @return mixed
     */
    public function create($type, array $arguments = array())
    {
        if ($this->_config->hasPlugins($type)) {
            return $this->_createInterceptor($type, $arguments, false);
        }
        return $this->_objectManager->create($type, $arguments);
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $type
     * @return mixed
     */
    public function get($type)
    {
        if ($this->_config->hasPlugins($type)) {
            if (!isset($this->_plugins[$type])) {
                $this->_plugins[$type] = $this->_createInterceptor($type);
            }
            return $this->_plugins[$type];
        }
        return $this->_objectManager->get($type);
    }

    /**
     * Configure object manager
     *
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        $this->_config->merge($configuration);
    }
}
