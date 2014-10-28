<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Model\Data\Eav;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;
use Magento\Framework\Service\Data\AttributeMetadataBuilderInterface;

/**
 * Class AttributeMetadataBuilder
 */
class AttributeMetadataBuilder extends AbstractExtensibleObjectBuilder implements AttributeMetadataBuilderInterface
{
    /**
     * Set attribute id
     *
     * @param  int $attributeId
     * @return $this
     */
    public function setAttributeId($attributeId)
    {
        return $this->_set(AttributeMetadata::ATTRIBUTE_ID, $attributeId);
    }

    /**
     * Set attribute code
     *
     * @param  string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(AttributeMetadata::ATTRIBUTE_CODE, $attributeCode);
    }
}
