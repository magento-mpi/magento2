<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_Generator_Proxy extends Magento_Di_Generator_EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'proxy';

    /**
     * @return array
     */
    protected function _getClassMethods()
    {
        $construct = $this->_getDefaultConstructorDefinition();

        // create proxy methods for all non-static and non-final public methods (excluding constructor)
        $methods         = array($construct);
        $reflectionClass = new ReflectionClass($this->_getSourceClassName());
        $publicMethods   = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            if (!($method->isConstructor() || $method->isFinal() || $method->isStatic())) {
                $methods[] = $this->_getMethodInfo($method);
            }
        }

        return $methods;
    }

    /**
     * @return string
     */
    protected function _generateCode()
    {
        $this->_classGenerator->setExtendedClass($this->_getSourceClassName());

        return parent::_generateCode();
    }

    /**
     * Collect method info
     *
     * @param ReflectionMethod $method
     * @return array
     */
    protected function _getMethodInfo(ReflectionMethod $method)
    {
        $parameterNames = array();
        $parameters     = array();
        foreach ($method->getParameters() as $parameter) {
            $parameterNames[] = '$' . $parameter->getName();
            $parameters[]     = $this->_getMethodParameterInfo($parameter);
        }

        $methodInfo = array(
            'name'       => $method->getName(),
            'parameters' => $parameters,
            'body' => $this->_getMethodBody($method->getName(), $parameterNames),
            'docblock' => array(
                'shortDescription' => '{@inheritdoc}',
            ),
        );

        return $methodInfo;
    }

    /**
     * Collect method parameter info
     *
     * @param ReflectionParameter $parameter
     * @return array
     */
    protected function _getMethodParameterInfo(ReflectionParameter $parameter)
    {
        $parameterInfo = array(
            'name'              => $parameter->getName(),
            'passedByReference' => $parameter->isPassedByReference()
        );

        if ($parameter->isArray()) {
            $parameterInfo['type'] = 'array';
        } elseif ($parameter->getClass()) {
            $parameterInfo['type'] = $parameter->getClass()->getName();
        }

        if ($parameter->isDefaultValueAvailable()) {
            $defaultValue = $parameter->getDefaultValue();
            if (is_string($defaultValue)) {
                $parameterInfo['defaultValue'] = $this->_escapeDefaultValue($parameter->getDefaultValue());
            } else {
                $parameterInfo['defaultValue'] = $parameter->getDefaultValue();
            }
        }

        return $parameterInfo;
    }

    /**
     * Escape method parameter default value
     *
     * @param string $value
     * @return string
     */
    protected function _escapeDefaultValue($value)
    {
        // escape single quotes quotes and slashes
        return sprintf("%s", addcslashes($value, "'\\"));
    }

    /**
     * Build proxy method body
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    protected function _getMethodBody($name, array $parameters = array())
    {
        if (count($parameters) == 0) {
            $methodCall = sprintf('%s()', $name);
        } else {
            $methodCall = sprintf('%s(%s)', $name, implode(', ', $parameters));
        }

        return 'return $this->_objectManager->get(self::CLASS_NAME)->' . $methodCall . ';';
    }
}
