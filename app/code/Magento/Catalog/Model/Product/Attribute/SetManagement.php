<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute;

use \Magento\Framework\Exception\StateException;

class SetManagement implements \Magento\Catalog\Api\AttributeSetManagementInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeSetManagementInterface
     */
    protected $attributeSetManagement;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param \Magento\Eav\Api\AttributeSetManagementInterface $attributeSetManagement
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetManagementInterface $attributeSetManagement,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->attributeSetManagement = $attributeSetManagement;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet, $skeletonId)
    {
        $this->validateSkeletonSet($skeletonId);
        return $this->attributeSetManagement->create(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSet,
            $skeletonId
        );
    }

    /**
     * @param $skeletonId
     * @return void
     * @throws StateException
     */
    protected function validateSkeletonSet($skeletonId)
    {
        $skeletonSet = $this->attributeSetRepository->get($skeletonId);
        $productEntityId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
        if ($skeletonSet->getEntityTypeId() != $productEntityId) {
            throw new StateException('Can not create attribute set based on non product attribute set.');
        }
    }
}
