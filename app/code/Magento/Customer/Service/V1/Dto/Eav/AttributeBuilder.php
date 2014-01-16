<?php
/**
 * Eav Attribute
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto\Eav;

use Magento\Customer\Service\V1\Dto\Eav\Attribute;

class AttributeBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param string $attributeCode
     * @return AttributeBuilder
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(Attribute::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * @param string $value
     * @return AttributeBuilder
     */
    public function setValue($value)
    {
        return $this->_set(Attribute::VALUE, $value);
    }
}
