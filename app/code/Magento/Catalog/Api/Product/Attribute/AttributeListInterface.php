<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Product\Attribute;

interface AttributeListInterface 
{
    /**
     * Retrieve list of product attribute types
     *
     * @return \Magento\Catalog\Api\Data\Product\Attribute\AttributeTypeInterface[]
     */
    public function getAttributeTypes();
}
