<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception\Code\Generator;

class Interceptor extends \Magento\Code\Generator\EntityAbstract
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
                'name' => '_objectManager',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'Object Manager instance',
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento\ObjectManager')
                    )
                ),
            ),
            array(
                'name' => '_pluginList',
                'visibility' => 'protected',
                'docblock' => array(
                    'shortDescription' => 'List of plugins',
                    'tags' => array(
                        array('name' => 'var', 'description' => '\Magento\Interception\PluginList')
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
        $reflectionClass = new \ReflectionClass($this->_getSourceClassName());
        $constructor = $reflectionClass->getConstructor();
        $parameters = array();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $parameters[] = $this->_getMethodParameterInfo($parameter);
            }
        }

        return array(
            'name'       => '__construct',
            'parameters' => array_merge(array(
                array('name' => 'objectManager', 'type' => '\Magento\ObjectManager\ObjectManager'),
                array('name' => 'pluginList', 'type' => '\Magento\Interception\PluginList'),
            ), $parameters),
            'body' => "\$this->_objectManager = \$objectManager;"
                . "\n\$this->_pluginList = \$pluginList;"
                . "\n\$this->_subjectType = get_parent_class(\$this);"
                . (count($parameters) ? "\nparent::__construct({$this->_getParameterList($parameters)});" : '')
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
            'name' => '___callParent',
            'parameters' => array(
                array('name' => 'method', 'type' => 'string'),
                array('name' => 'arguments', 'type' => 'array'),
            ),
            'body' => 'return call_user_func_array(array(\'parent\', $method), $arguments);'
        );

        $methods[] = array(
            'name' => '__sleep',
            'body' => "if (method_exists(get_parent_class(\$this), '__sleep')) {\n"
                . "    return parent::__sleep();\n"
                . "} else {\n"
                . "    return array_keys(get_class_vars(get_parent_class(\$this)));\n"
                . "}\n"
        );

        $methods[] = array(
            'name' => '__wakeup',
            'body' => "\$this->_objectManager = \\Magento\\App\\ObjectManager::getInstance();\n"
                . "\$this->_pluginList = \$this->_objectManager->get('Magento\\Interception\\PluginList');\n"
                . "\$this->_subjectType = get_parent_class(\$this);\n"
        );

        $methods[] = array(
            'name' => '_getDecorator',
            'visibility' => 'protected',
            'parameters' => array(
                array('name' => 'code', 'type' => 'string'),
            ),
            'body' => "if (!isset(\$this->_decorators[\$code])) {\n"
                . "    \$this->_decorators[\$code] = new Decorator(\n"
                . "        \$this, \$this->_objectManager, \$this->_subjectType,\n"
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
            'docblock' => array(
                'tags' => array(
                    array('name' => 'var', 'description' => 'self')
                )
            ),
            'body' => "\$capMethod = ucfirst(\$method);\n"
                . "\$result = null;\n"
                . "if (isset(\$pluginInfo[\\Magento\\Interception\\Definition::LISTENER_BEFORE])) {\n"
                . "    foreach (\$pluginInfo[\\Magento\\Interception\\Definition::LISTENER_BEFORE] as \$code) {\n"
                . "        \$beforeResult = call_user_func_array(\n"
                . "            array(\$this->_pluginList->getPlugin(\$this->_subjectType, \$code), 'before'"
                    . ". \$capMethod), array_merge(array(\$this), \$arguments)\n"
                . "        );\n"
                . "        if (\$beforeResult) {\n"
                . "            \$arguments = \$beforeResult;\n"
                . "        }\n"
                . "    }\n"
                . "}\n"
                . "if (isset(\$pluginInfo[\\Magento\\Interception\\Definition::LISTENER_AROUND])) {\n"
                . "    \$decorator = \$this->_getDecorator(\$pluginInfo["
                    . "\\Magento\\Interception\\Definition::LISTENER_AROUND]);\n"
                . "    \$next = function() use (\$decorator, \$method) {\n"
                . "        return call_user_func_array(array(\$decorator, \$method), func_get_args());\n"
                . "    };\n"
                . "    \$result = call_user_func_array(\n"
                . "        array(\$this->_pluginList->getPlugin(\$this->_subjectType,"
                    . " \$pluginInfo[\\Magento\\Interception\\Definition::LISTENER_AROUND]), 'around' . \$capMethod),\n"
                . "        array_merge(array(\$this, \$next), \$arguments)\n"
                . "    );\n"
                . "} else {\n"
                . "    \$result = call_user_func_array(array('parent', \$method), \$arguments);\n"
                . "}\n"
                . "if (isset(\$pluginInfo[\\Magento\\Interception\\Definition::LISTENER_AFTER])) {\n"
                . "    foreach (\$pluginInfo[\\Magento\\Interception\\Definition::LISTENER_AFTER] as \$code) {\n"
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
            'body' => "\$pluginInfo = \$this->_pluginList->getNext(\$this->_subjectType, '{$method->getName()}');\n"
                . "if (!\$pluginInfo) {\n"
                . "    return parent::{$method->getName()}({$this->_getParameterList($parameters)});\n"
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
                    return "$" . $item['name'];
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

            if ($resultClassName !== $sourceClassName . '\\Interceptor') {
                $this->_addError('Invalid Interceptor class name ['
                    . $resultClassName . ']. Use ' . $sourceClassName . '\\Interceptor'
                );
                $result = false;
            }
        }
        return $result;
    }
}
