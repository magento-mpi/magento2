<?php
/**
 * Class constructor validator. Validates call of parent construct
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Validator;

use Magento\Code\ValidatorInterface;

class ConstructorIntegrity implements ValidatorInterface
{
    /**
     * @var \Magento\Code\Reader\ArgumentsReader
     */
    protected $_argumentsReader;

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
     * @throws \Magento\Code\ValidationException
     */
    public function validate($className)
    {
        $class = new \ReflectionClass($className);
        $parent = $class->getParentClass();

        /** Check whether parent class exists and has __construct method */
        if (!$parent) {
            return true;
        }

        /** Get parent class __construct arguments */
        $parentArguments = $this->_argumentsReader->getConstructorArguments($parent, true, true);
        if (empty($parentArguments)) {
            return true;
        }

        /** Check whether class has __construct */
        $classArguments = $this->_argumentsReader->getConstructorArguments($class);
        if (null === $classArguments) {
            return true;
        }

        /** Check whether class has parent::__construct call */
        $callArguments = $this->_argumentsReader->getParentCall($class, $classArguments);
        if (null === $callArguments) {
            return true;
        }

        /** Get parent class __construct arguments */
        $parentArguments = $this->_argumentsReader->getConstructorArguments($parent, true, true);

        foreach ($parentArguments as $index => $requiredArgument) {
            if (isset($callArguments[$index])) {
                $actualArgument = $callArguments[$index];
            } else {
                if ($requiredArgument['isOptional']) {
                    continue;
                }

                throw new \Magento\Code\ValidationException('Missed required argument ' . $requiredArgument['name']
                    . ' in parent::__construct call. File: ' . $class->getFileName()
                );
            }

            $isCompatibleTypes = $this->_argumentsReader->isCompatibleType(
                $requiredArgument['type'],
                $actualArgument['type']
            );
            if (false == $isCompatibleTypes) {
                throw new \Magento\Code\ValidationException('Incompatible argument type: Required type: '
                    . $requiredArgument['type'] . '. Actual type: ' . $actualArgument['type']
                    . '; File: ' . PHP_EOL .$class->getFileName() . PHP_EOL
                );
            }
        }

        /**
         * Try to detect unused arguments
         * Check whether count of passed parameters less or equal that count of count parent class arguments
         */
        if (count($callArguments) > count($parentArguments)) {
            $extraParameters = array_slice($callArguments, count($parentArguments));
            $names = array();
            foreach ($extraParameters as $param) {
                $names[] = '$' . $param['name'];
            }

            throw new \Magento\Code\ValidationException(
                'Extra parameters passed to parent construct: '
                . implode(', ', $names)
                . '. File: ' . $class->getFileName()
            );
        }
        return true;
    }

}
