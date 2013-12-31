<?php
/**
 * Eav Attribute
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1\Eav;

interface EntityInterface
{
    /**
     * @return string[]
     */
    public function getAttributes();

    /**
     * @param string[] $attributes
     * @return EntityInterface
     */
    public function setAttributes(array $attributes);

    /**
     * @param string $attributeCode
     * @param string $attributeValue
     * @return EntityInterface
     */
    public function setAttribute($attributeCode, $attributeValue);

    /**
     * @param string $attributeCode
     * @return string|null
     */
    public function getAttribute($attributeCode);

}