<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

/**
 * Interface AttributeSetManagement must be implemented
 * in new model \Magento\Eav\Model\AttributeSetManagement
 */
interface AttributeSetManagementInterface
{
    /**
     * Create attribute set from data
     *
     * @param string $entityType
     * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet
     * @param int $skeletonId
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::create
     */
    public function create(
        $entityType,
        \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet,
        $skeletonId
    );
}
