<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Category\Attribute;

interface OptionManagementInterface
{
    /**
     * Retrieve list of attribute options
     *
     * @param string $attributeId
     * @return  \Magento\Catalog\Api\Data\Eav\AttributeOptionInterface[]
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @todo maybe, get rid of this interface because AttributeMetadataInterface has getOptions() method.
     */
    public function getList($attributeId);
}