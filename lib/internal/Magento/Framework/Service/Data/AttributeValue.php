<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Data;

/**
 * Custom Attribute Data object
 */
class AttributeValue extends AbstractSimpleObject implements AttributeValueInterface
{
    /**#@+
     * Constant used as key into $_data
     */
    const ATTRIBUTE_CODE = 'attribute_code';
    const VALUE = 'value';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
