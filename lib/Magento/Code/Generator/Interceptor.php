<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Code_Generator_Interceptor extends Magento_Code_Generator_EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'interceptor';

    /**
     * @param string $modelClassName
     * @return string
     */
    protected function _getDefaultResultClassName($modelClassName)
    {
        return $modelClassName . '_' . ucfirst(static::ENTITY_TYPE);
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
                'name' => '_factory',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'Object Manager factory',
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento_ObjectManager_Factory')
                    )
                ),
            ),
            array(
                'name' => '_objectManager',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'Object Manager instance',
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento_ObjectManager')
                    )
                ),
            ),
            array(
                'name' => '_subjectType',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'Subject type',
                    'tags' => array(
                        array('name' => 'var', 'description' => 'string')
                    )
                ),
            ),
            array(
                'name' => '_subject',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'Subject',
                    'tags' => array(
                        array('name' => 'var', 'description' => '\\' . $this->_getSourceClassName())
                    )
                ),
            ),
            array(
                'name' => '_pluginList',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'List of plugins',
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento_Interception_PluginList')
                    )
                ),
            ),
            array(
                'name' => '_arguments',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'Subject constructor arguments',
                    'tags' => array(
                        array('name' => 'var', 'description' => 'array')
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
                array('name' => 'factory', 'type' => '\Magento_ObjectManager_Factory'),
                array('name' => 'objectManager', 'type' => '\Magento_ObjectManager_ObjectManager'),
                array('name' => 'subjectType'),
                array('name' => 'pluginList', 'type' => '\Magento_Interception_PluginList'),
                array('name' => 'arguments', 'type' => 'array'),
            ),
            'body' => "\$this->_factory = \$factory;"
                . "\n\$this->_objectManager = \$objectManager;"
                . "\n\$this->_subjectType = \$subjectType;"
                . "\n\$this->_pluginList = \$pluginList;"
                . "\n\$this->_arguments = \$arguments;",
            'docblock' => array(
                'shortDescription' => 'Interceptor constructor',
                'tags' => array(
                    array(
                        'name' => 'param',
                        'description' => '\Magento_ObjectManager_Factory $factory',
                    ),
                    array(
                        'name' => 'param',
                        'description' => '\Magento_ObjectManager_ObjectManager $objectManager',
                    ),
                    array(
                        'name' => 'param',
                        'description' => 'string $subjectType',
                    ),
                    array(
                        'name' => 'param',
                        'description' => '\Magento_Interception_PluginList $pluginList',
                    ),
                    array(
                        'name' => 'param',
                        'description' => 'array $arguments',
                    ),
                ),
            ),
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
            'name' => '_getSubject',
            'visibility' => 'protected',
            'body' => 'if (is_null($this->_subject)) {'
                . "\n    \$this->_subject = \$this->_factory->create(\$this->_subjectType, \$this->_arguments);"
                . "\n}"
                . "\nreturn \$this->_subject;",
            'docblock' => array(
                'shortDescription' => 'Retrieve subject',
                'tags' => array(
                    array('name' => 'return', 'description' => 'mixed'),
                )
            ),
        );
        $methods[] = array(
            'name'=> '_invoke',
            'visibility' => 'protected',
            'parameters' => array(
                array('name' => 'methodName'),
                array('name' => 'methodArguments', 'type' => 'array'),
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
            'name' => '__sleep',
            'body' => "\$this->_getSubject();\nreturn array('_subject', '_subjectType');",
            'docblock' => array(
                'tags' => array(
                    array(
                        'name' => 'return', 'description' => 'array',
                    )
                ),
            ),
        );
        $methods[] = array(
            'name' => '__clone',
            'body' => "\$this->_subject = clone \$this->_getSubject();",
            'docblock' => array(
                'shortDescription' => 'Clone subject instance',
            ),
        );
        $methods[] = array(
            'name' => '__wakeup',
            'docblock' => array(
                'shortDescription' => 'Retrieve ObjectManager from the global scope',
            ),
            'body' => '$this->_objectManager = Magento_Core_Model_ObjectManager::getInstance();'
                . "\n\$this->_pluginList = \$this->_objectManager->get('Magento_Interception_PluginList');",
        );

        $reflectionClass = new ReflectionClass($this->_getSourceClassName());
        $publicMethods   = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
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
        return "\$beforeMethodName = 'before' . \$methodName;"
            . "\nforeach (\$this->_pluginList->getPlugins(\$this->_subjectType, \$methodName, 'before') as \$plugin) {"
            . "\n    \$methodArguments = \$this->_objectManager->get(\$plugin)"
            . "\n        ->\$beforeMethodName(\$methodArguments);"
            . "\n}"
            . "\n\$invocationChain = new \\Magento_Code_Plugin_InvocationChain("
            . "\n    \$this->_getSubject(),"
            . "\n    \$methodName,"
            . "\n    \$this->_objectManager,"
            . "\n    \$this->_pluginList->getPlugins(\$this->_subjectType, \$methodName, 'around')"
            . "\n);"
            . "\n\$invocationResult = \$invocationChain->proceed(\$methodArguments);"
            . "\n\$afterMethodName = 'after' . \$methodName;"
            . "\n\$afterPlugins = array_reverse("
            . "\n    \$this->_pluginList->getPlugins(\$this->_subjectType, \$methodName, 'after')"
            . "\n);"
            . "\nforeach (\$afterPlugins as \$plugin) {"
            . "\n    \$invocationResult = \$this->_objectManager->get(\$plugin)"
            . "\n        ->\$afterMethodName(\$invocationResult);"
            . "\n}"
            . "\nreturn \$invocationResult;";
    }

    /**
     * Retrieve method info
     *
     * @param ReflectionMethod $method
     * @return array
     */
    protected function _getMethodInfo(ReflectionMethod $method)
    {
        $parameters = array();
        foreach ($method->getParameters() as $parameter) {
            $parameters[] = $this->_getMethodParameterInfo($parameter);
        }

        $methodInfo = array(
            'name' => $method->getName(),
            'parameters' => $parameters,
            'body' => "return \$this->_invoke('{$method->getName()}', func_get_args());",
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
        $reflection = new ReflectionClass($typeName);

        if ($reflection->isInterface()) {
            $this->_classGenerator->setImplementedInterfaces(array($typeName));
        } else {
            $this->_classGenerator->setExtendedClass($typeName);
        }
        return parent::_generateCode();
    }
}
