<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

/**
 * Interface RepositoryInterface must be implemented in new model
 */
interface CategoryAttributeRepositoryInterface extends \Magento\Framework\Service\Data\MetadataServiceInterface
{
    /**
     * Retrieve all attributes for entity type
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Data\Search\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Retrieve specific attribute
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\CategoryAttributeInterface
     */
    public function get($attributeCode);
}
