<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Data;

/**
 * Default implementation of the AttributeMetadataBuilderInterface
 */
class AttributeMetadataBuilder extends AbstractObjectBuilder implements AttributeMetadataBuilderInterface
{
    const ATTRIBUTE_CODE = 'attribute_code';

    /**
     * Set attribute code
     *
     * @param  string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(self::ATTRIBUTE_CODE, $attributeCode);
    }
}
