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
        Magento_ObjectManager_Definition $definition,
        array $configuration = array(),
        array $sharedInstances = array()
    ) {
        $this->_factory = new Magento_ObjectManager_Factory_InterceptionDecorator(
            new Magento_ObjectManager_Factory($this, $definition),
            $this
        );
        $this->configure($configuration);
    }

    /**
     * Resolve Class name
     *
     * @param string $className
     * @return string
     * @throws LogicException
     */
    protected function _resolvePreferences($className)
    {
        $preferencePath = array();
        while (isset($this->_preferences[$className])) {
            if (isset($preferencePath[$this->_preferences[$className]])) {
                throw new LogicException(
                    'Circular type preference: ' . $className . ' relates to '
                        . $this->_preferences[$className] . ' and viceversa.'
                );
            }
            $className = $this->_preferences[$className];
            $preferencePath[$className] = 1;
        }
        return $className;
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
        if (isset($this->_preferences[$type])) {
            $type = $this->_resolvePreferences($type);
        }
        return $this->_factory->create($type, $arguments);
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $type
     * @return mixed
     */
    public function get($type)
    {
        if (isset($this->_preferences[$type])) {
            $type = $this->_resolvePreferences($type);
        }
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
        if (isset($configuration['preferences'])) {
            $this->_preferences = array_replace($this->_preferences, $configuration['preferences']);
            unset($configuration['preferences']);
        }
        $this->_factory->configure($configuration);
    }
}
