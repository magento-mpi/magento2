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
namespace Magento\ObjectManager;

class ObjectManager implements \Magento\ObjectManager
{
    /**
     * @var \Magento\ObjectManager\Factory
     */
    protected $_factory;

    /**
     * List of shared instances
     *
     * @var array
     */
    protected $_sharedInstances = array();

    /**
     * @param \Magento\ObjectManager\Factory $factory
     * @param \Magento\ObjectManager\Config $config
     * @param array $sharedInstances
     */
    public function __construct(
        \Magento\ObjectManager\Factory $factory = null,
        \Magento\ObjectManager\Config $config = null,
        array $sharedInstances = array()
    ) {
        $this->_config = $config ?: new \Magento\ObjectManager\Config\Config();
        $this->_factory = $factory ?: new \Magento\ObjectManager\Factory\Factory($this->_config, $this);
        $this->_factory->setObjectManager($this);
        $this->_sharedInstances = $sharedInstances;
        $this->_sharedInstances['Magento\ObjectManager'] = $this;
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
