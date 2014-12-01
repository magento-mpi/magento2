<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Model;

interface AttributeTypeResolverInterface
{
    /**
     * Resolve attribute type
     *
     * @param string $attributeCode
     * @param object $value
     * @param string $context
     * @return string
     */
    public function resolveObjectType($attributeCode, $value, $context);
}
