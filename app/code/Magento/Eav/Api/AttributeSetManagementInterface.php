<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

interface AttributeSetManagementInterface
{
    /**
     * Create attribute set from data
     *
     * @param string $entityTypeCode
     * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet
     * @param int $skeletonId
     * @return \Magento\Eav\Api\Data\AttributeSetInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function create(
        $entityTypeCode,
        \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet,
        $skeletonId
    );
}
