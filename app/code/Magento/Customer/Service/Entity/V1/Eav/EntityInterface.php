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
     * @param string $attributeCode
     * @return string|null
     */
    public function getAttribute($attributeCode);

}
