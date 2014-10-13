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
 * Interface AttributeSetManagement must be implemented
 * in new model \Magento\Catalog\Model\AttributeSetManagement
 */
interface ProductAttributeSetManagementInterface
{
    /**
     * Create attribute set from data
     *
     * @param \Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet
     * @param int $skeletonId
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::create
     */
    public function create(\Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet, $skeletonId);
}
