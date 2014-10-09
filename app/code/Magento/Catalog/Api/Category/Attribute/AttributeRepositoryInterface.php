<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Category\Attribute;

interface AttributeRepositoryInterface
{
    /**
     * Retrieve full information about attribute
     *
     * @param string $attributeId
     * @return \Magento\Catalog\Api\Data\Eav\AttributeMetadataInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($attributeId);

    /**
     * Retrieve the list of product attributes
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResults
     * containing \Magento\Catalog\Api\Data\Eav\AttributeMetadataInterface objects
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}