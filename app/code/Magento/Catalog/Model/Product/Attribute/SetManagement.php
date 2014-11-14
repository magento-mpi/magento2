<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute;

class SetManagement implements \Magento\Catalog\Api\AttributeSetManagementInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeSetManagementInterface
     */
    protected $attributeSetManagement;

    /**
     * @param \Magento\Eav\Api\AttributeSetManagementInterface $attributeSetManagement
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetManagementInterface $attributeSetManagement
    ) {
        $this->attributeSetManagement = $attributeSetManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet, $skeletonId)
    {
        return $this->attributeSetManagement->create(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSet,
            $skeletonId
        );
    }
}
