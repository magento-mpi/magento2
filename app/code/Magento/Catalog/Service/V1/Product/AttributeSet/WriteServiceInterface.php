<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeSet;

use Magento\Catalog\Service\V1\Data\Eav\AttributeSet;

/**
 * Interface WriteServiceInterface
 * Service interface to create/update/remove product attribute sets
 */
interface WriteServiceInterface
{
    /**
     * Create attribute set from data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeSet $attributeSet
     * @param int $skeletonId
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Eav\Api\AttributeSetManagementInterface::create
     */
    public function create(AttributeSet $attributeSet, $skeletonId);

    /**
     * Update attribute set data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeSet $attributeSetData
     * @return int attribute set ID
     * @throws \Magento\Framework\Model\Exception If attribute set is not found
     * @deprecated
     * @see \Magento\Eav\Api\AttributeSetRepositoryInterface::save
     */
    public function update(AttributeSet $attributeSetData);

    /**
     * Remove attribute set by id
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     * @deprecated
     * @see \Magento\Eav\Api\AttributeSetRepositoryInterface::delete
     */
    public function remove($attributeSetId);
}
