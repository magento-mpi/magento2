<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Code\Generator;

use Magento\Framework\Code\Generator\EntityAbstract;

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
        return [];
    }

    /**
     * Returns list of methods for class generator
     *
     * @return array
     */
    protected function _getClassMethods()
    {
        $methods = [];
        $className = $this->_getSourceClassName();
        $reflectionClass = new \ReflectionClass($className);
        $lowerClassName = strtolower($className);
        $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            //since methods are sorted - lowest inheritance first, no need to loop through everything
            if ($lowerClassName !== strtolower($method->class)) {
                break;
            }
            if (!($method->isConstructor() ||
                    $method->isFinal() ||
                    $method->isStatic() ||
                    $method->isDestructor()) &&
                !in_array(
                    $method->getName(),
                    array('__sleep', '__wakeup', '__clone')
                ) &&
                $method->class !== 'Magento\Framework\Api\ExtensibleDataInterface'
            ) {
                if (substr($method->getName(), 0, 3) == 'get') {
                    $methods[] = $this->_getMethodInfo($reflectionClass, $method);
                }

            }
        }
        return $methods;
    }

    /**
     * Retrieve method info
     *
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @return array
     */
    protected function _getMethodInfo(\ReflectionClass $class, \ReflectionMethod $method)
    {
        $methodInfo = [
            'name' => 'set' . substr($method->getName(), 3),
            'parameters' => [
                ['name' => lcfirst(substr($method->getName(), 3))]
            ],
            'body' => "\$this->_set("
                . '\\' . $class->getName() . "::"
                . strtoupper(preg_replace('/(.)([A-Z])/', "$1_$2", substr($method->getName(), 3)))
                . ", \$" . lcfirst(substr($method->getName(), 3)) . ");",
            'docblock' => array('shortDescription' => '{@inheritdoc}')
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

            if ($resultClassName !== $sourceClassName . 'Builder') {
                $this->_addError(
                    'Invalid Builder class name [' . $resultClassName . ']. Use ' . $sourceClassName . 'Builder'
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
        )->setExtendedClass('\\Magento\Framework\Api\ExtensibleDataBuilderInterface');

        return $this->_getGeneratedCode();
    }
}
