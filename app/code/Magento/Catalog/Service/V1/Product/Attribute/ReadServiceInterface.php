<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

/**
 * Class ReadServiceInterface
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
interface ReadServiceInterface
{
    /**
     * Retrieve list of product attribute types
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\Type[]
     * @deprecated
     * @see \Magento\Catalog\Api\Product\Attribute\AttributeManagementInterface::getAttributeTypes
     */
    public function types();

    /**
     * Get full information about a required attribute with the list of options
     *
     * @param  string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Catalog\Api\Product\Attribute\AttributeRepositoryInterface::get
     */
    public function info($id);

    /**
     * Retrieve the list of product attributes
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResults containing Data\Eav\Attribute objects
     * @deprecated
     * @see \Magento\Catalog\Api\Product\Attribute\AttributeRepositoryInterface::getList
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
