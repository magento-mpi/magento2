<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Framework\Data\SearchCriteria;
use Magento\Framework\Service\V1\Data\SearchResults;

/**
 * Class MetadataServiceInterface
 */
interface MetadataServiceInterface
{
    /**
     * Retrieve Attribute Metadata
     *
     * @param  string $entityType
     * @param  string $attributeCode
     * @return AttributeMetadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttributeMetadata($entityType, $attributeCode);

    /**
     * Returns all known attributes metadata for a entity corresponding to $searchCriteria
     *
     * @param  string $entityType
     * @param SearchCriteria $searchCriteria
     * @return SearchResults
     */
    public function getAllAttributeMetadata($entityType, SearchCriteria $searchCriteria);
}
