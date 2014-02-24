<?php
/**
 * Eav Attribute
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;

interface EntityInterface
{
    /**
     * @param string $attributeCode
     * @return string|null
     */
    public function getAttribute($attributeCode);
}
