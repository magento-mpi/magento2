<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Code\Generator;

/**
 * Class Repository
 */
class Repository extends \Magento\Framework\Code\Generator\EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'repository';

    /**
     * Retrieve class properties
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        $properties = [
            [
                'name' => $this->_getSourceFactoryPropertyName(),
                'visibility' => 'protected',
                'docblock' => [
                    'shortDescription' =>  $this->_getSourceFactoryPropertyName(),
                    'tags' => [
                        [
                            'name' => 'var',
                            'description' =>
                                $this->_getFullyQualifiedClassName($this->_getSourceClassName()) . 'Factory'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'registry',
                'visibility' => 'protected',
                'defaultValue' => [],
                'docblock' => [
                    'shortDescription' => $this->_getSourceClassName() . '[]',
                    'tags' => [['name' => 'var', 'description' => 'array']]
                ]
            ]
        ];
        return $properties;
    }

    /**
     * Returns source factory property Name
     *
     * @return string
     */
    protected function _getSourceFactoryPropertyName()
    {
        $parts = explode('\\', $this->_getSourceClassName());
        return lcfirst(end($parts)) . 'Factory';
    }

    /**
     * Get default constructor definition for generated class
     *
     * @return array
     */
    protected function _getDefaultConstructorDefinition()
    {
        return [
            'name' => '__construct',
            'parameters' => [
                [
                    'name' => $this->_getSourceFactoryPropertyName(),
                    'type' => $this->_getFullyQualifiedClassName($this->_getSourceClassName()) . 'Factory'
                ],
            ],
            'body' => "\$this->"
                . $this->_getSourceFactoryPropertyName()
                . " = \$" . $this->_getSourceFactoryPropertyName() . ';',
            'docblock' => [
                'shortDescription' => ucfirst(static::ENTITY_TYPE) . ' constructor',
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => '\\' . $this->_getSourceClassName()
                            . " \$" . $this->_getSourceFactoryPropertyName()
                    ]
                ]
            ]
        ];
    }

    /**
     * Returns list of methods for class generator
     *
     * @return array
     */
    protected function _getClassMethods()
    {
        $construct = $this->_getDefaultConstructorDefinition();

        $body = "if (!\$id) {\n"
        . "\tthrow new \\Magento\Framework\Exception\NoSuchEntityException('Requested product doesn\\'t exist');\n"
        . "}\n"
        . "if (!isset(\$this->registry[\$id])) {\n"
        . "\t\$this->registry[\$id] = \$this->"
        . $this->_getSourceFactoryPropertyName()
        . "->create()->load(\$id);\n"
        . "}\n"
        . "return \$this->registry[\$id];";

        $get = [
            'name' => 'get',
            'parameters' => [['name' => 'id', 'type' => 'int']],
            'body' => $body,
            'docblock' => [
                'shortDescription' => 'load entity',
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => 'int $id'
                    ],
                    [
                        'name' => 'return',
                        'description' => $this->_getFullyQualifiedClassName($this->_getSourceClassName()),
                    ],
                    [
                        'name' => 'throws',
                        'description' => '\\Magento\Framework\Exception\NoSuchEntityException',
                    ]
                ]
            ]
        ];
        return array($construct, $get);
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

            if ($resultClassName !== $sourceClassName . 'Repository') {
                $this->_addError(
                    'Invalid Factory class name [' . $resultClassName . ']. Use ' . $sourceClassName . 'Repository'
                );
                $result = false;
            }
        }
        return $result;
    }
}
