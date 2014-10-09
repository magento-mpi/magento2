<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeSet;

interface ReadServiceInterface
{
    /**
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeSet[]
     * @deprecated
     * @see \Magento\Catalog\Api\Product\AttributeSetRepositoryInterface::getList
     */
    public function getList();

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeSet
     * @deprecated
     * @see \Magento\Catalog\Api\Product\Attribute\AttributeSetRepositoryInterface::get
     */
    public function getInfo($attributeSetId);

    /**
     * Retrieve related attributes based on given attribute set ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Service\V1\Data\Eav\Attribute[]
     * @deprecated
     * @see \Magento\Catalog\Api\Product\Attribute\AttributeSetManagementInterface::getAttributeList
     */
    public function getAttributeList($attributeSetId);
}
