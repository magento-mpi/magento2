<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Profiler\Code\Generator;

class Logger extends \Magento\Framework\Code\Generator\EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'logger';

    /**
     * @param string $modelClassName
     * @return string
     */
    protected function _getDefaultResultClassName($modelClassName)
    {
        return $modelClassName . '\\' . ucfirst(static::ENTITY_TYPE);
    }

    /**
     * Returns list of properties for class generator
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        return array(
            array(
                'name' => 'log',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'Object Manager factory log',
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento\ObjectManager\Factory\Log')
                    )
                ),
            ),
            array(
                'name' => 'subject',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'Object Manager instance',
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento\ObjectManager')
                    )
                ),
            ),
        );
    }

    /**
     * Get default constructor definition for generated class
     *
     * @return array
     */
    protected function _getDefaultConstructorDefinition()
    {
        return array(
            'name'       => '__construct',
            'parameters' => array(
                array('name' => 'subject'),
                array('name' => 'log')
            ),
            'body' => "\$this->log = \$log;"
                . "\n\$this->subject = \$subject;"
                //. "\n\$this->log->add(\$subject);"
        );
    }

    /**
     * Returns list of methods for class generator
     *
     * @return mixed
     */
    protected function _getClassMethods()
    {
        $methods = array($this->_getDefaultConstructorDefinition());
        $methods[] = array(
            'name'=> '_invoke',
            'visibility' => 'protected',
            'parameters' => array(
                array('name' => 'methodName'),
                array('name' => 'methodArguments', 'type' => 'array', 'passedByReference' => true),
            ),
            'body' => $this->_getInvokeMethodBody(),
            'docblock' => array(
                'shortDescription' => 'Invoke method',
                'tags' => array(
                    array('name' => 'param', 'description' => 'string $methodName'),
                    array('name' => 'param', 'description' => 'array $methodArguments'),
                    array('name' => 'return', 'description' => 'mixed'),
                ),
            ),
        );
        $methods[] = array(
            'name' => '__clone',
            'body' => "\$this->subject = clone \$this->subject;"
                . "\n\$this->log->add(\$this->subject);",
            'docblock' => array(
                'shortDescription' => 'Clone subject instance',
            ),
        );

        $methods[] = array(
            'name' => '__sleep',
            'body' => "return array('subject');",
        );

        $methods[] = array(
            'name' => '__wakeUp',
            'body' => "\$this->log = \\Magento\\Framework\\ObjectManager\\Profiler\\Log::getInstance();"
                ."\n\$this->log->add(\$this->subject);",
        );

        $reflectionClass = new \ReflectionClass($this->_getSourceClassName());
        $publicMethods   = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            if (!($method->isConstructor() || $method->isFinal() || $method->isStatic() || $method->isDestructor())
                && !in_array($method->getName(), array('__sleep', '__wakeup', '__clone'))
            ) {
                $methods[] = $this->_getMethodInfo($method);
            }
        }

        return $methods;
    }

    /**
     * Retrieve body of the _invoke method
     *
     * @return string
     */
    protected function _getInvokeMethodBody()
    {
        return "\n\$this->log->invoked(\$this->subject, \$methodName);"
        . "\n\$result = call_user_func_array(array(\$this->subject, \$methodName), \$methodArguments);"
        . "\nif (\$result === \$this->subject) {"
        . "\n    return \$this;"
        . "\n}"
        . "\nreturn \$result;";
    }

    /**
     * Retrieve method info
     *
     * @param \ReflectionMethod $method
     * @return array
     */
    protected function _getMethodInfo(\ReflectionMethod $method)
    {
        $parameters = array();
        foreach ($method->getParameters() as $parameter) {
            $parameters[] = $this->_getMethodParameterInfo($parameter);
        }

        $body = "\$args = func_get_args();";
        foreach ($parameters as $key => $parameter) {
            if ($parameter['passedByReference']) {
                $body .= "\$args[$key] = &\$" . $parameter['name'] . ';';
            }
        }

        $methodInfo = array(
            'name' => $method->getName(),
            'parameters' => $parameters,
            'body' => $body . "\nreturn \$this->_invoke('{$method->getName()}', \$args);",
            'docblock' => array(
                'shortDescription' => '{@inheritdoc}',
            ),
        );

        return $methodInfo;
    }

    /**
     * Generate resulting class source code
     *
     * @return string
     */
    protected function _generateCode()
    {
        $typeName = $this->_getFullyQualifiedClassName($this->_getSourceClassName());
        $reflection = new \ReflectionClass($typeName);

        if ($reflection->isInterface()) {
            $this->_classGenerator->setImplementedInterfaces(array($typeName));
        } else {
            $this->_classGenerator->setExtendedClass($typeName);
        }
        return parent::_generateCode();
    }

    /**
     * {@inheritdoc}
     */
    protected function _validateData()
    {
        $result = parent::_validateData();

        if ($result) {
            $sourceClassName = $this->_getSourceClassName();
            $resultClassName = $this->_getResultClassName();

            if ($resultClassName !== $sourceClassName . '\\Logger') {
                $this->_addError('Invalid Logger class name ['
                    . $resultClassName . ']. Use ' . $sourceClassName . '\\Logger'
                );
                $result = false;
            }
        }
        return $result;
    }
}
