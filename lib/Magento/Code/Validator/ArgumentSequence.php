<?php
/**
 * Class constructor validator. Validates arguments sequence
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Validator;

use Magento\Code\Generator\ConstructorBuilder;
use Magento\Code\Reader\ArgumentsReader;
use Magento\Code\ValidatorInterface;
use Magento\Code\ValidationException;

class ArgumentSequence implements ValidatorInterface
{
    /**
     * @var \Magento\Code\Reader\ArgumentsReader
     */
    protected $_argumentsReader;

    /**
     * List of allowed type to validate
     * @var array
     */
    protected $_allowedTypes = array('\Magento\App\Action\Action', '\Magento\View\Element\BlockInterface');

    /**
     * @var array
     */
    protected $_cache;

    /**
     * @param \Magento\Code\Reader\ArgumentsReader $argumentsReader
     */
    public function __construct(\Magento\Code\Reader\ArgumentsReader $argumentsReader = null)
    {
        $this->_argumentsReader = $argumentsReader ?: new \Magento\Code\Reader\ArgumentsReader();
    }

    /**
     * Validate class
     *
     * @param string $className
     * @return bool
     * @throws ValidationException
     */
    public function validate($className)
    {
        /** Temporary solution. Need to be removed since all AC of MAGETWO-14343 will be covered */
        if (!$this->_isAllowedType($className)) {
            return true;
        }

        $class = new \ReflectionClass($className);
        $classArguments = $this->_argumentsReader->getConstructorArguments($class);

        if ($this->_isContextOnly($classArguments)) {
            return true;
        }

        $parent = $class->getParentClass();
        $parentArguments = array();
        if ($parent) {
            $parentClass = $parent->getName();
            if (0 !== strpos($parentClass, '\\')) {
                $parentClass = '\\' . $parentClass;
            }

            if (isset($this->_cache[$parentClass])) {
                $parentArguments = $this->_cache[$parentClass];
            } else {
                $parentArguments = $this->_argumentsReader->getConstructorArguments($parent, false, true);
            }
        }

        $requiredSequence = $this->_buildsSequence($classArguments, $parentArguments);
        $this->_cache[$className] = $requiredSequence;

        if (false == $this->_checkArgumentSequence($classArguments, $requiredSequence)) {
            throw new ValidationException(
                'Incorrect argument sequence in class ' . $className . ' in ' . $class->getFileName() . PHP_EOL
                . 'Required: $' . implode(', $', array_keys($requiredSequence)) . PHP_EOL
                . 'Actual  : $' . implode(', $', array_keys($classArguments)) . PHP_EOL
            );
        }

        return true;
    }

    /**
     * Check whether type can be validated
     *
     * @param string $className
     * @return bool
     */
    protected function _isAllowedType($className)
    {
        foreach ($this->_allowedTypes as $type) {
            if (is_subclass_of($className, $type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check argument sequence
     *
     * @param array $actualSequence
     * @param array $requiredSequence
     * @return bool
     */
    protected function _checkArgumentSequence(array $actualSequence, array $requiredSequence)
    {
        $actual = array_keys($actualSequence);
        $required = array_keys($requiredSequence);
        return $actual === $required;
    }

    /**
     * Build argument required sequence
     *
     * @param array $classArguments
     * @param array $parentArguments
     * @return array
     */
    protected function _buildsSequence(array $classArguments, array $parentArguments = array())
    {
        if (empty($classArguments)) {
            return $classArguments;
        }

        $classArgumentList = $this->_sortArguments($classArguments);
        $parentArgumentList = $this->_sortArguments($parentArguments);

        $migratedArgs = array();
        $output = array();

        foreach (array('object', 'scalar') as $argumentType) {
            $classArguments = $classArgumentList[$argumentType];
            $parentArguments = $parentArgumentList[$argumentType];

            $classArguments[ArgumentsReader::REQUIRED] = isset($classArguments[ArgumentsReader::REQUIRED])
                ? $classArguments[ArgumentsReader::REQUIRED]
                : array();

            $classArguments[ArgumentsReader::OPTIONAL] = isset($classArguments[ArgumentsReader::OPTIONAL])
                ? $classArguments[ArgumentsReader::OPTIONAL]
                : array();

            $parentArguments[ArgumentsReader::REQUIRED] = isset($parentArguments[ArgumentsReader::REQUIRED])
                ? $parentArguments[ArgumentsReader::REQUIRED]
                : array();

            $parentArguments[ArgumentsReader::OPTIONAL] = isset($parentArguments[ArgumentsReader::OPTIONAL])
                ? $parentArguments[ArgumentsReader::OPTIONAL]
                : array();

            foreach ($parentArguments[ArgumentsReader::REQUIRED] as $name => $argument) {
                if (!isset($classArguments[ArgumentsReader::OPTIONAL][$name])) {
                    $output[$name] = isset($classArguments[ArgumentsReader::REQUIRED][$name])
                        ? $classArguments[ArgumentsReader::REQUIRED][$name]
                        : $argument;
                } else {
                    $migratedArgs[$name] =  $classArguments[ArgumentsReader::OPTIONAL][$name];
                }
            }

            foreach ($classArguments[ArgumentsReader::REQUIRED] as $name => $argument) {
                if (!isset($output[$name])) {
                    $output[$name] = $argument;
                }
            }

            foreach ($migratedArgs as $name => $argument) {
                if (!isset($output[$name])) {
                    $output[$name] = $argument;
                }
            }

            foreach ($parentArguments[ArgumentsReader::OPTIONAL] as $name => $argument) {
                if (!isset($output[$name])) {
                    $output[$name] = isset($classArguments[ArgumentsReader::OPTIONAL][$name])
                        ? $classArguments[ArgumentsReader::OPTIONAL][$name]
                        : $argument;
                }
            }

            foreach ($classArguments[ArgumentsReader::OPTIONAL] as $name => $argument) {
                if (!isset($output[$name])) {
                    $output[$name] = $argument;
                }
            }
        }

        return $output;
    }

    /**
     * Sort arguments
     *
     * @param array $arguments
     * @return array
     */
    protected function _sortArguments($arguments)
    {
        $requiredObject = array();
        $requiredScalar = array();
        $optionalObject = array();
        $optionalScalar = array();

        foreach ($arguments as $argument) {
            if ($argument['type'] && $argument['type'] != 'array') {
                if ($argument['isOptional']) {
                    $optionalObject[$argument['name']] = $argument;
                } else {
                    $requiredObject[$argument['name']] = $argument;
                }
            } else {
                if ($argument['isOptional']) {
                    $optionalScalar[$argument['name']] = $argument;
                } else {
                    $requiredScalar[$argument['name']] = $argument;
                }
            }
        }

        $requiredObject = $this->_sortObjectType($requiredObject);
        $optionalScalar = $this->_sortScalarType($optionalScalar);

        return array(
            'object' => array(
                ArgumentsReader::REQUIRED => $requiredObject, ArgumentsReader::OPTIONAL => $optionalObject
            ),
            'scalar' => array(
                ArgumentsReader::REQUIRED => $requiredScalar, ArgumentsReader::OPTIONAL => $optionalScalar
            ),
        );
    }

    /**
     * Sort arguments by context object
     *
     * @param array $argumentList
     * @return array
     */
    protected function _sortObjectType(array $argumentList)
    {
        $context = array();
        foreach ($argumentList as $name => $argument) {
            if ($this->_isContextType($argument['type'])) {
                $context[$name] = $argument;
                unset($argumentList[$name]);
                break;
            }
        }
        return array_merge($context, $argumentList);
    }

    /**
     * Sort arguments by arguments name
     *
     * @param array $argumentList
     * @return array
     */
    protected function _sortScalarType(array $argumentList)
    {
        $data = array();
        foreach ($argumentList as $name => $argument) {
            if ($argument['name'] == 'data') {
                $data[$name] = $argument;
                unset($argumentList[$name]);
                break;
            }
        }
        return array_merge($data, $argumentList);
    }

    /**
     * Check whether arguments list contains an only context argument
     *
     * @param array $arguments
     * @return bool
     */
    protected function _isContextOnly(array $arguments)
    {
        if (count($arguments) !== 1) {
            return false;
        }
        $argument = current($arguments);
        return $argument['type'] && $this->_isContextType($argument['type']);
    }

    /**
     * Check whether type is context object
     *
     * @param string $type
     * @return bool
     */
    protected function _isContextType($type)
    {
        return is_subclass_of($type, '\Magento\ObjectManager\ContextInterface');
    }
}
