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

    /**
     * Runtime configuration
     *
     * @var Magento_ObjectManager_Config
     */
    protected $_configuration;

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
     * @param Magento_ObjectManager_Config $configuration
     */
    public function __construct(
        Magento_ObjectManager_Definition $definitions = null,
        array $configuration = array()
    ) {
        $this->_definitions = $definitions ?: new Magento_ObjectManager_Definition_Runtime();
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
        if (isset($this->_configuration[$className]['parameters'])) {
            $arguments = array_replace($this->_configuration[$className]['parameters'], $arguments);
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
                $argument = $this->_isShared($argument) ?
                    $this->get($argument) :
                    $this->create($argument);
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
        while (isset($this->_configuration['preferences'][$className])) {
            if (isset($preferencePath[$this->_configuration['preferences'][$className]])) {
                throw new LogicException(
                    'Circular type preference: ' . $className . ' relates to '
                        . $this->_configuration['preferences'][$className] . ' and viceversa.'
                );
            }
            $className = $this->_configuration['preferences'][$className];
            $preferencePath[$className] = 1;
        }
        return $className;
    }

    /**
     * @param $className
     * @return bool
     */
    protected function _isShared($className)
    {
        return !(isset($this->_configuration[$className]['shared'])
            && $this->_configuration[$className]['shared'] == false);
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

        switch(count($args)) {
            case 1:
                return new $resolvedClassName($args[0]);

            case 2:
                return new $resolvedClassName($args[0], $args[1]);

            case 3:
                return new $resolvedClassName($args[0], $args[1], $args[2]);

            case 4:
                return new $resolvedClassName($args[0], $args[1], $args[2], $args[3]);

            case 5:
                return new $resolvedClassName($args[0], $args[1], $args[2], $args[3], $args[4]);

            case 6:
                return new $resolvedClassName($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);

            case 7:
                return new $resolvedClassName($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);

            case 8:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]
                );

            case 9:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]
                );

            case 10:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]
                );

            case 11:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10]
                );

            case 12:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11]
                );

            case 13:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12]
                );

            case 14:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13]
                );

            case 15:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13], $args[14]
                );

            case 16:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13], $args[14], $args[15]
                );

            case 17:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13], $args[14], $args[15], $args[16]
                );

            case 18:
                return new $resolvedClassName(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13], $args[14], $args[15], $args[16], $args[17]
                );

            default:
                $reflection = new \ReflectionClass($resolvedClassName);
                return $reflection->newInstanceArgs($args);
        }
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
        return $this->_create($this->_resolveClassName($className), $arguments);
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $className
     * @return mixed
     */
    public function get($className)
    {
        $resolvedName = $this->_resolveClassName($className);
        if (!isset($this->_sharedInstances[$resolvedName])) {
            $this->_sharedInstances[$resolvedName] = $this->_create($resolvedName);
        }
        return $this->_sharedInstances[$resolvedName];
    }

    /**
     * Configure di instance
     *
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        $this->_configuration = array_replace_recursive($this->_configuration, $configuration);
    }
}
