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

interface EntityInterface
{
    /**
     * @return string[]
     */
    public function getAttributes();

    /**
     * @param string $attributeCode
     * @return string|null
     */
    public function getAttribute($attributeCode);

}
