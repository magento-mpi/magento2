<?php
/**
 * Magento object manager. Responsible for instantiating objects taking itno account:
 * - constructor arguments (using configured, and provided parameters)
 * - class shareability
 * - interface preferences
 *
 * Intentionally contains multiple concerns for optimum performance
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_ObjectManager implements Magento_ObjectManager
{
    /**
     * Class definitions
     *
     * @var Magento_ObjectManager_Definition
     */
    protected $_definitions;

    protected $_arguments = array();
    
    protected $_nonShared = array();

    protected $_preferences = array();

    /**
     * List of classes being created
     *
     * @var array
     */
    protected $_creationStack = array();

    /**
     * List of shared instances
     *
     * @var array
     */
    protected $_sharedInstances = array();

    /**
     * @param Magento_ObjectManager_Definition $definitions
     * @param array $configuration
     * @param array $sharedInstances
     */
    public function __construct(
        Magento_ObjectManager_Definition $definitions = null,
        array $configuration = array(),
        array $sharedInstances = array()
    ) {
        $this->_definitions = $definitions ?: new Magento_ObjectManager_Definition_Runtime();
        $this->_sharedInstances = $sharedInstances;
        $this->_sharedInstances['Magento_ObjectManager'] = $this;
        $this->_configuration = $configuration;
    }


    /**
     * Resolve constructor arguments
     *
     * @param string $className
     * @param array $parameters
     * @param array $arguments
     * @return array
     * @throws LogicException
     * @throws BadMethodCallException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _resolveArguments($className, array $parameters, array $arguments = array())
    {
        $resolvedArguments = array();
        if (isset($this->_arguments[$className])) {
            $arguments = array_replace($this->_arguments[$className], $arguments);
        }
        foreach ($parameters as $parameter) {
            list($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
            $argument = null;
            if (array_key_exists($paramName, $arguments)) {
                $argument = $arguments[$paramName];
            } else if ($paramRequired) {
                if ($paramType) {
                    $argument = $paramType;
                } else {
                    throw new BadMethodCallException(
                        'Missing required argument $' . $paramName . ' for ' . $className . '.'
                    );
                }
            } else {
                $argument = $paramDefault;
            }
            if ($paramRequired && $paramType && !is_object($argument)) {
                if (isset($this->_creationStack[$argument])) {
                    throw new LogicException(
                        'Circular dependency: ' . $argument . ' depends on ' . $className . ' and viceversa.'
                    );
                }

                $this->_creationStack[$className] = 1;
                $argument = isset($this->_nonShared[$argument]) ?
                    $this->create($argument) :
                    $this->get($argument);
                unset($this->_creationStack[$className]);
            }
            $resolvedArguments[] = $argument;
        }
        return $resolvedArguments;
    }

    /**
     * @return string
     * @throws LogicException
     */
    protected function _resolveClassName($className)
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
     * Create instance with call time arguments
     *
     * @param string $resolvedClassName
     * @param array $arguments
     * @return object
     * @throws LogicException
     * @throws BadMethodCallException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _create($resolvedClassName, array $arguments = array())
    {
        $parameters = $this->_definitions->getParameters($resolvedClassName);
        if ($parameters == null) {
            return new $resolvedClassName();
        }
        $args = $this->_resolveArguments($resolvedClassName, $parameters, $arguments);
        $reflection = new \ReflectionClass($resolvedClassName);
        return $reflection->newInstanceArgs($args);
    }

    /**
     * Create new object instance
     *
     * @param string $className
     * @param array $arguments
     * @return mixed
     */
    public function create($className, array $arguments = array())
    {
        if (isset($this->_preferences[$className])) {
            $className = $this->_resolveClassName($className);
        }
        return $this->_create($className, $arguments);
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $className
     * @return mixed
     */
    public function get($className)
    {
        if (isset($this->_preferences[$className])) {
            $className = $this->_resolveClassName($className);
        }
        if (!isset($this->_sharedInstances[$className])) {
            $this->_sharedInstances[$className] = $this->_create($className);
        }
        return $this->_sharedInstances[$className];
    }

    /**
     * Configure di instance
     *
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        foreach ($configuration as $key => $curConfig) {
            switch ($key) {
                case 'preferences':
                    $this->_preferences = array_replace($this->_preferences, $curConfig);
                    break;

                default:
                    if (isset($curConfig['parameters'])) {
                        if (isset($this->_arguments[$key])) {
                            $this->_arguments[$key] = array_replace($this->_arguments[$key], $curConfig['parameters']);
                        } else {
                            $this->_arguments[$key] = $curConfig['parameters'];
                        }
                    }
                    if (isset($curConfig['shared'])) {
                        if (!$curConfig['shared'] || $curConfig['shared'] == 'false') {
                            $this->_nonShared[$key] = 1;
                        } else {
                            unset($this->_nonShared[$key]);
                        }
                    }
                    break;
            }
        }
    }
}
