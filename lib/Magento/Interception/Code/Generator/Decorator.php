<?php
/**
 * Decorator class generator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception\Code\Generator;

class Decorator extends \Magento\Code\Generator\EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'decorator';

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
                'name' => '_subject',
                'visibility' => 'protected',
                'docblock' => array(
                    'tags' => array(
                        array('name' => 'var', 'description' => $this->_getSourceClassName())
                    )
                ),
            ),
            array(
                'name' => '_objectManager',
                'visibility' => 'protected',
                'docblock' => array(
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento\ObjectManager')
                    )
                ),
            ),
            array(
                'name' => '_subjectType',
                'visibility' => 'protected',
                'docblock' => array(
                    'tags' => array(
                        array('name' => 'var', 'description' => 'string')
                    )
                ),
            ),
            array(
                'name' => '_pluginList',
                'visibility' => 'protected',
                'docblock' => array(
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento\Interception\PluginList')
                    )
                ),
            ),
            array(
                'name' => '_code',
                'visibility' => 'protected',
                'docblock' => array(
                    'tags' => array(
                        array('name' => 'var', 'description' => 'string')
                    )
                ),
            ),
            array(
                'name' => '_decorators',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'List of decorators',
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
                array('name' => 'subject', 'type' => '\\' . $this->_getSourceClassName()),
                array('name' => 'objectManager', 'type' => '\Magento\ObjectManager\ObjectManager'),
                array('name' => 'subjectType', 'type' => 'string'),
                array('name' => 'pluginList', 'type' => '\Magento\Interception\PluginList'),
                array('name' => 'code', 'type' => 'string'),
            ),
            'body' => "\$this->_objectManager = \$objectManager;"
                . "\n\$this->_pluginList = \$pluginList;"
                . "\n\$this->_subjectType = \$subjectType;"
                . "\n\$this->_code = \$code;"
                . "\n\$this->_subject = \$subject;"
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
            'name' => '_getDecorator',
            'visibility' => 'protected',
            'parameters' => array(
                array('name' => 'code', 'type' => 'string'),
            ),
            'body' => "if (!isset(\$this->_decorators[\$code])) {\n"
                . "    \$this->_decorators[\$code] = new Decorator(\n"
                . "        \$this->_subject, \$this->_objectManager, \$this->_subjectType,\n"
                . "        \$this->_pluginList, \$code\n"
                . "    );\n"
                . "}\n"
                . "return \$this->_decorators[\$code];\n"
        );

        $methods[] = array(
            'name' => '___call',
            'visibility' => 'protected',
            'parameters' => array(
                array('name' => 'method', 'type' => 'string'),
                array('name' => 'arguments', 'type' => 'array'),
                array('name' => 'pluginInfo', 'type' => 'array'),
            ),
            'body' => "\$capMethod = ucfirst(\$method);\n"
                . "\$result = null;\n"
                . "if (isset(\$pluginInfo[0])) {\n"
                . "    foreach (\$pluginInfo[0] as \$code) {\n"
                . "        \$beforeResult = call_user_func_array(\n"
                . "            array(\$this->_pluginList->getPlugin(\$this->_subjectType, \$code), 'before'"
                . ". \$capMethod), array_merge(array(\$this), \$arguments)\n"
                . "        );\n"
                . "        if (\$beforeResult) {\n"
                . "            \$arguments = \$beforeResult;\n"
                . "        }\n"
                . "    }\n"
                . "}\n"
                . "if (isset(\$pluginInfo[1])) {\n"
                . "    \$decorator = \$this->_getDecorator(\$pluginInfo[1]);"
                . "    \$next = function() use (\$decorator, \$method) {"
                . "        return call_user_func_array(array(\$decorator, \$method), func_get_args());"
                . "    };"
                . "    \$result = call_user_func_array(\n"
                . "        array(\$this->_pluginList->getPlugin(\$this->_subjectType, \$pluginInfo[1]),"
                . " 'around' . \$capMethod),\n"
                . "        array_merge(array(\$this, \$next), \$arguments)\n"
                . "    );\n"
                . "} else {\n"
                . "     return call_user_func_array(array(\$this->_subject, '___callParent'),"
                    . "array(\$method, \$arguments));\n"
                . "}\n"
                . "if (isset(\$pluginInfo[2])) {\n"
                . "    foreach (\$pluginInfo[2] as \$code) {\n"
                . "        \$result = \$this->_pluginList->getPlugin(\$this->_subjectType, \$code)\n"
                . "             ->{'after' . \$capMethod}(\$this, \$result);\n"
                . "    }\n"
                . "}\n"
                . "return \$result;\n"
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

        $methodInfo = array(
            'name' => $method->getName(),
            'parameters' => $parameters,
            'body' => "\$pluginInfo = \$this->_pluginList->getNext(\$this->_subjectType,"
                    ."'{$method->getName()}', \$this->_code);\n"
                . "if (!\$pluginInfo) {\n"
                . "     return call_user_func_array(array(\$this->_subject, '___callParent'),"
                    . " array('{$method->getName()}', func_get_args()));\n"
                . "} else {\n"
                . "    return \$this->___call('{$method->getName()}', func_get_args(), \$pluginInfo);\n"
                . "}",
            'docblock' => array(
                'shortDescription' => '{@inheritdoc}',
            ),
        );

        return $methodInfo;
    }

    /**
     * @param array $parameters
     * @return string
     */
    protected function _getParameterList(array $parameters)
    {
        return implode(
            ', ',
            array_map(
                function($item) {
                    return $item['name'];
                },
                $parameters
            )
        );
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

            if ($resultClassName !== $sourceClassName . '\\Decorator') {
                $this->_addError('Invalid Plugin Decorator class name ['
                    . $resultClassName . ']. Use ' . $sourceClassName . '\\Decorator'
                );
                $result = false;
            }
        }
        return $result;
    }
}
