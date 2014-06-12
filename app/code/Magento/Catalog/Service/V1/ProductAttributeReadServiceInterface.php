<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

/**
 * Class ProductAttributeReadServiceInterface
 * @package Magento\Catalog\Service\V1
 */
interface ProductAttributeReadServiceInterface
{
    /**
     * Retrieve list of product attribute types
     *
     * @return \Magento\Catalog\Service\V1\Data\ProductAttributeType[]
     */
    public function types();

    /**
     * Get full information about a required attribute with the list of options
     *
     * @param  string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function info($id);

    /**
     * Retrive the list of product attributes
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResults containing Data\Eav\Attribute objects
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
