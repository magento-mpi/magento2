<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Factory
{
    /**
     * @var Magento_ObjectManager_ObjectManager
     */
    protected $_objectManager;

    /**
     * Definition list
     *
     * @var Magento_ObjectManager_Definition
     */
    protected $_definitions;

    /**
     * List of configured arguments
     *
     * @var array
     */
    protected $_arguments = array();

    /**
     * List of non-shared types
     *
     * @var array
     */
    protected $_nonShared = array();

    /**
     * List of virtual types
     *
     * @var array
     */
    protected $_virtualTypes = array();

    /**
     * List of classes being created
     *
     * @var array
     */
    protected $_creationStack = array();

    /**
     * @param Magento_ObjectManager_ObjectManager $objectManager
     * @param Magento_ObjectManager_Definition $definitions
     */
    public function __construct(
        Magento_ObjectManager_ObjectManager $objectManager, Magento_ObjectManager_Definition $definitions = null
    ) {
        $this->_objectManager = $objectManager;
        $this->_definitions = $definitions ?: new Magento_ObjectManager_Definition_Runtime();
    }

    /**
     * Resolve constructor arguments
     *
     * @param string $requestedType
     * @param array $parameters
     * @param array $arguments
     * @return array
     * @throws LogicException
     * @throws BadMethodCallException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _resolveArguments($requestedType, array $parameters, array $arguments = array())
    {
        $resolvedArguments = array();
        if (isset($this->_arguments[$requestedType])) {
            $arguments = array_replace($this->_arguments[$requestedType], $arguments);
        }
        foreach ($parameters as $parameter) {
            list($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
            $argument = null;
            if (array_key_exists($paramName, $arguments)) {
                $argument = $arguments[$paramName];
            } else if ($paramRequired) {
                if ($paramType) {
                    $argument = array('instance' => $paramType);
                } else {
                    throw new BadMethodCallException(
                        'Missing required argument $' . $paramName . ' for ' . $requestedType . '.'
                    );
                }
            } else {
                $argument = $paramDefault;
            }
            if ($paramRequired && $paramType && !is_object($argument)) {
                if (!is_array($argument) || !isset($argument['instance'])) {
                    throw new InvalidArgumentException(
                        'Invalid parameter configuration provided for $' . $paramName . ' argument in ' . $requestedType
                    );
                }
                $argumentType = $argument['instance'];
                if (isset($this->_creationStack[$argumentType])) {
                    throw new LogicException(
                        'Circular dependency: ' . $argumentType . ' depends on ' . $requestedType . ' and viceversa.'
                    );
                }
                $this->_creationStack[$requestedType] = 1;

                $isShared = (!isset($argument['shared']) && !isset($this->_nonShared[$argumentType]))
                    || (isset($argument['shared']) && $argument['shared'] && $argument['shared'] != 'false');
                $argument = $isShared
                    ? $this->_objectManager->get($argumentType)
                    : $this->_objectManager->create($argumentType);
                unset($this->_creationStack[$requestedType]);
            }
            $resolvedArguments[] = $argument;
        }
        return $resolvedArguments;
    }


    /**
     * Resolve instance name
     *
     * @param string $instanceName
     * @return string
     */
    protected function _resolveInstanceType($instanceName)
    {
        while (isset($this->_virtualTypes[$instanceName])) {
            $instanceName = $this->_virtualTypes[$instanceName];
        }
        return $instanceName;
    }

    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array $arguments
     * @return object
     * @throws LogicException
     * @throws BadMethodCallException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function create($requestedType, array $arguments = array())
    {
        $type = $this->_resolveInstanceType($requestedType);
        $parameters = $this->_definitions->getParameters($type);
        if ($parameters == null) {
            return new $type();
        }
        $args = $this->_resolveArguments($requestedType, $parameters, $arguments);

        switch(count($args)) {
            case 1:
                return new $type($args[0]);

            case 2:
                return new $type($args[0], $args[1]);

            case 3:
                return new $type($args[0], $args[1], $args[2]);

            case 4:
                return new $type($args[0], $args[1], $args[2], $args[3]);

            case 5:
                return new $type($args[0], $args[1], $args[2], $args[3], $args[4]);

            case 6:
                return new $type($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);

            case 7:
                return new $type($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);

            case 8:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]
                );

            case 9:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]
                );

            case 10:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]
                );

            case 11:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10]
                );

            case 12:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11]
                );

            case 13:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12]
                );

            case 14:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13]
                );

            case 15:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13], $args[14]
                );

            case 16:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13], $args[14], $args[15]
                );

            case 17:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13], $args[14], $args[15], $args[16]
                );

            case 18:
                return new $type(
                    $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9],
                    $args[10], $args[11], $args[12], $args[13], $args[14], $args[15], $args[16], $args[17]
                );

            default:
                $reflection = new \ReflectionClass($type);
                return $reflection->newInstanceArgs($args);
        }
    }

    /**
     * Configure factory
     *
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        foreach ($configuration as $key => $curConfig) {
            if (isset($curConfig['type'])) {
                $this->_virtualTypes[$key] = $curConfig['type'];
            }
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
        }
    }
}
