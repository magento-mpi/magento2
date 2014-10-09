<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\Attribute;

/**
 * Class ReadServiceInterface
 */
interface ReadServiceInterface
{
    /**
     * Retrieve list of attribute options
     *
     * @param string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option[]
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Api\Category\Attribute\OptionManagementInterface::getList
     */
    public function options($id);

    /**
     * Get full information about a required attribute with the list of options
     *
     * @param  string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Api\Category\Attribute\AttributeRepositoryInterface::get
     */
    public function info($id);

    /**
     * Retrieve the list of product attributes
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResults containing Data\Eav\Attribute objects
     * @see \Magento\Catalog\Api\Category\Attribute\AttributeRepositoryInterface::getList
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
