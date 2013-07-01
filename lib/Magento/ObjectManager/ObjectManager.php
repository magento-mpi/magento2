<?php
/**
 * Magento object manager. Responsible for instantiating objects taking itno account:
 * - constructor arguments (using configured, and provided parameters)
 * - class instances life style (singleton, transient)
 * - interface preferences
 *
 * Intentionally contains multiple concerns for best performance
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_ObjectManager implements Magento_ObjectManager
{
    /**
     * @var Magento_ObjectManager_Factory
     */
    protected $_factory;

    /**
     * Interface preferences
     *
     * @var array
     */
    protected $_preferences = array();

    /**
     * List of shared instances
     *
     * @var array
     */
    protected $_sharedInstances = array();

    /**
     * @param Magento_ObjectManager_Definition $definition
     * @param array $configuration
     * @param array $sharedInstances
     */
    public function __construct(
        Magento_ObjectManager_Definition $definition = null,
        array $configuration = array(),
        array $sharedInstances = array()
    ) {
        $this->_config = new Magento_ObjectManager_Config();
        $this->_config->extend($configuration);
        $this->_factory = new Magento_ObjectManager_Interception_FactoryDecorator(
            new Magento_ObjectManager_Factory($this, $this->_config, $definition),
            $this,
            $this->_config
        );
        $this->_sharedInstances = $sharedInstances;
        $this->_sharedInstances['Magento_ObjectManager'] = $this;
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
        return $this->_factory->create($this->_config->getPreference($type), $arguments);
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $type
     * @return mixed
     */
    public function get($type)
    {
        $type = $this->_config->getPreference($type);
        if (!isset($this->_sharedInstances[$type])) {
            $this->_sharedInstances[$type] = $this->_factory->create($type);
        }
        return $this->_sharedInstances[$type];
    }

    /**
     * Configure di instance
     *
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        $this->_config->extend($configuration);
    }
}
