<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Code\Generator;

/**
 * Class Repository
 */
class Mapper extends \Magento\Framework\Code\Generator\EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'mapper';

    /**
     * Retrieve class properties
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        $properties = [
            [
                'name' => $this->_getSourceBuilderPropertyName(),
                'visibility' => 'protected',
                'docblock' => [
                    'shortDescription' =>  $this->_getSourceBuilderPropertyName(),
                    'tags' => [
                        [
                            'name' => 'var',
                            'description' =>
                                $this->_getFullyQualifiedClassName($this->_getSourceClassName()) . 'Builder'
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
    protected function _getSourceBuilderPropertyName()
    {
        $parts = explode('\\', $this->_getSourceClassName());
        return lcfirst(end($parts)) . 'Builder';
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
                    'name' => $this->_getSourceBuilderPropertyName(),
                    'type' => $this->_getFullyQualifiedClassName($this->_getSourceClassName()) . 'Builder'
                ],
            ],
            'body' => "\$this->"
                . $this->_getSourceBuilderPropertyName()
                . " = \$" . $this->_getSourceBuilderPropertyName() . ';',
            'docblock' => [
                'shortDescription' => ucfirst(static::ENTITY_TYPE) . ' constructor',
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => '\\' . $this->_getSourceClassName()
                            . " \$" . $this->_getSourceBuilderPropertyName()
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
        $body = "\$this->" . $this->_getSourceBuilderPropertyName() . "->populateWithArray(\$object->getData());"
            . "return \$this->" . $this->_getSourceBuilderPropertyName() . "->create();";
        $extract = [
            'name' => 'get',
            'parameters' => [
                [
                    'name' => 'object',
                    'type' => '\\Magento\Framework\Model\AbstractModel'
                ]
            ],
            'body' => $body,
            'docblock' => [
                'shortDescription' => 'Extract data object from model',
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => '\\Magento\Framework\Model\AbstractModel $object'
                    ],
                    [
                        'name' => 'return',
                        'description' => $this->_getFullyQualifiedClassName($this->_getSourceClassName()),
                    ]
                ]
            ]
        ];
        return array($construct, $extract);
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

            if ($resultClassName !== $sourceClassName . 'Mapper') {
                $this->_addError(
                    'Invalid Mapper class name [' . $resultClassName . ']. Use ' . $sourceClassName . 'Mapper'
                );
                $result = false;
            }
        }
        return $result;
    }
}
