<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Code\Generator;

/**
 * Class Converter
 * @package Magento\Framework\ObjectManager\Code\Generator
 */
class Converter extends \Magento\Framework\Code\Generator\EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'converter';

    /**
     * Retrieve class properties
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        return [
            [
                'name' => $this->_getFactoryPropertyName(),
                'visibility' => 'protected',
                'docblock' => [
                    'shortDescription' => $this->_getFactoryPropertyName(),
                    'tags' => [
                        [
                            'name' => 'var',
                            'description' =>
                                $this->_getFactoryClass()
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Returns factory name
     *
     * @return string
     */
    protected function _getFactoryPropertyName()
    {
        $parts = explode('\\', $this->_getSourceClassName());
        return lcfirst(end($parts)) . 'Factory';
    }

    /**
     * Returns factory class
     *
     * @return string
     */
    protected function _getFactoryClass()
    {
        return $this->_getFullyQualifiedClassName($this->_getSourceClassName()) . 'Factory';
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
                    'name' => $this->_getFactoryPropertyName(),
                    'type' => $this->_getFactoryClass()
                ],
            ],
            'body' => "\$this->"
                . $this->_getFactoryPropertyName()
                . " = \$" . $this->_getFactoryPropertyName() . ';',
            'docblock' => [
                'shortDescription' => ucfirst(static::ENTITY_TYPE) . ' constructor',
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => '\\' . $this->_getSourceClassName()
                            . " \$" . $this->_getFactoryPropertyName()
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
        $paramName = 'dataObject';
        $body = 'return $this->' . $this->_getFactoryPropertyName()
            . '->create()->setData($' . $paramName .'->__toArray());';
        $getModel = [
            'name' => 'getModel',
            'parameters' => [
                [
                    'name' => $paramName,
                    'type' => '\Magento\Framework\Service\Data\AbstractExtensibleObject'
                ]
            ],
            'body' => $body,
            'docblock' => [
                'shortDescription' => 'Extract data object from model',
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => '\Magento\Framework\Service\Data\AbstractExtensibleObject $' . $paramName,
                    ],
                    [
                        'name' => 'return',
                        'description' => $this->_getFullyQualifiedClassName($this->_getSourceClassName())
                    ]
                ]
            ]
        ];
        return array($construct, $getModel);
    }

    /**
     * {@inheritdoc}
     */
    protected function _validateData()
    {
        if (!parent::_validateData()) {
            return false;
        }

        $sourceClassName = $this->_getSourceClassName();
        $resultClassName = $this->_getResultClassName();

        if ($resultClassName !== $sourceClassName . 'Converter') {
            $this->_addError(
                'Invalid Converter class name [' . $resultClassName . ']. Use ' . $sourceClassName . 'Converter'
            );
            return false;
        }
        return true;
    }
}
