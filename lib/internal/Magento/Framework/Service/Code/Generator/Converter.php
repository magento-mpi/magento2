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
        $dtoParameterName = '';
        $body = 'return $this->' . $this->_getFactoryPropertyName()
            . '->create()->setData((array)' . $dtoParameterName .');';
        $getModel = [
            'name' => 'getModel',
            'parameters' => [
                [
                    'name' => 'dto',
                    'type' => $this->_getFullyQualifiedClassName($this->_getSourceClassName())
                ]
            ],
            'body' => $body,
            'docblock' => [
                'shortDescription' => 'Extract data object from model',
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => $this->_getFullyQualifiedClassName($this->_getSourceClassName()),
                    ],
                    [
                        'name' => 'return',
                        'description' => '\\Magento\Framework\Model\AbstractModel $object'
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
                'Invalid Mapper class name [' . $resultClassName . ']. Use ' . $sourceClassName . 'Mapper'
            );
            return false;
        }
        return true;
    }
}
