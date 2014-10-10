<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

interface ReadServiceInterface
{
    /**
     * Retrieve list of attribute groups
     *
     * @param string $attributeSetId
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Catalog\Api\Product\Attribute\AttributeGroupRepositoryInterface::getList
     */
    public function getList($attributeSetId);
}
