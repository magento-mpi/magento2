<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;
use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Interface RepositoryInterface must be implemented in new model
 */
interface CategoryAttributeRepositoryInterface
{
    /**
     * Retrieve all attributes for entity type
     *
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Data\Search\SearchResultsInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAllAttributeMetadata
     */
    public function getList(\Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve specific attribute
     *
     * @param string $identifier
     * @return \Magento\Catalog\Api\Data\CategoryAttributeInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAttributeMetadata
     */
    public function get($identifier);
}
