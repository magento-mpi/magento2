<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Code\Generator;

use Magento\Framework\Code\Generator\EntityAbstract;
use Zend\Code\Reflection\ClassReflection;

/**
 * Class Builder
 */
class DataBuilder extends EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'dataBuilder';

    /**
     * Data Model property name
     */
    const DATA_PROPERTY_NAME = 'data';

    /**
     * Retrieve class properties
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        return [];
    }

    /**
     * Get default constructor definition for generated class
     *
     * @return array
     */
    protected function _getDefaultConstructorDefinition()
    {
        $constructorDefinition = [
            'name' => '__construct',
            'parameters' => [
                ['name' => 'objectManager', 'type' => '\Magento\Framework\ObjectManager']
            ],
            'docblock' => [
                'shortDescription' => 'Initialize the builder',
                'tags' => [['name' => 'param', 'description' => '\Magento\Framework\ObjectManager $objectManager']]
            ],
            'body' => "parent::__construct(\$objectManager, "
                . "'" . $this->_getSourceClassName() . "');"
        ];

        return $constructorDefinition;
    }

    /**
     * Return a list of methods for class generator
     *
     * @return array
     */
    protected function _getClassMethods()
    {
        $methods = [];
        $className = $this->_getSourceClassName();
        $reflectionClass = new \ReflectionClass($className);
        $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            if ($this->canUseMethodForGeneration($method)) {
                $methods[] = $this->_getMethodInfo($method);
            }
        }
        $methods[] = $this->_getDefaultConstructorDefinition();
        return $methods;
    }

    /**
     * Check if the specified method should be used during generation builder generation.
     *
     * @param \ReflectionMethod $method
     * @return bool
     */
    protected function canUseMethodForGeneration($method)
    {
        $isGetter = (substr($method->getName(), 0, 3) == 'get');
        $isSuitableMethodType = !($method->isConstructor() || $method->isFinal()
            || $method->isStatic() || $method->isDestructor());
        $isExcludedFromGeneration = in_array($method->getName(), array('__sleep', '__wakeup', '__clone'));
        $isSuitableClass = $method->class !== 'Magento\Framework\Api\Data\ExtensibleDataInterface';
        return $isGetter && $isSuitableMethodType && !$isExcludedFromGeneration && $isSuitableClass;
    }

    /**
     * Retrieve method info
     *
     * @param \ReflectionMethod $method
     * @return array
     */
    protected function _getMethodInfo(\ReflectionMethod $method)
    {
        $propertyName = substr($method->getName(), 3);

        $returnType = (new ClassReflection($this->_getSourceClassName()))
            ->getMethod($method->getName())
            ->getDocBlock()
            ->getTag('return')
            ->getType();

        $methodInfo = [
            'name' => 'set' . $propertyName,
            'parameters' => [
                ['name' => lcfirst($propertyName)]
            ],
            'body' => "\$this->" . self::DATA_PROPERTY_NAME . "['"
                . strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $propertyName))
                . "'] = \$" . lcfirst($propertyName) . ";" . PHP_EOL . "return \$this;",
            'docblock' => array('shortDescription' => '@param ' . $returnType . " \$" . lcfirst($propertyName))
        ];

        return $methodInfo;
    }

    /**
     * Validate data
     *
     * @return bool
     */
    protected function _validateData()
    {
        $result = parent::_validateData();

        if ($result) {
            $sourceClassName = $this->_getSourceClassName();
            $resultClassName = $this->_getResultClassName();

            if ($resultClassName !== str_replace('Interface', '', $sourceClassName) . ucfirst(self::ENTITY_TYPE)) {
                $this->_addError(
                    'Invalid Builder class name [' . $resultClassName . ']. Use '
                    . $sourceClassName
                    . ucfirst(self::ENTITY_TYPE)
                );
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Generate code
     *
     * @return string
     */
    protected function _generateCode()
    {
        $this->_classGenerator->setName(
            $this->_getResultClassName()
        )->addProperties(
            $this->_getClassProperties()
        )->addMethods(
            $this->_getClassMethods()
        )->setClassDocBlock(
            $this->_getClassDocBlock()
        )->setExtendedClass('\Magento\Framework\Service\Data\ExtensibleDataBuilder');

        return $this->_getGeneratedCode();
    }

    /**
     * @return string
     */
    protected function _getSourceClassName()
    {
        return parent::_getSourceClassName() . 'Interface';
    }
}
