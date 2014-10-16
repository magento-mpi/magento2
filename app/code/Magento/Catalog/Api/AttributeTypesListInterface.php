<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

interface AttributeTypesListInterface
{
    /**
     * Retrieve list of product attribute types
     *
     * @return \Magento\Catalog\Api\Data\AttributeTypeInterface[]
     */
    public function getItems();
}
