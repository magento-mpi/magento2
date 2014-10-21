<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model;

use \Magento\Eav\Api\AttributeSetManagementInterface;
use \Magento\Eav\Api\Data\AttributeSetInterface;
use \Magento\Eav\Model\Config as EavConfig;
use \Magento\Framework\Exception\InputException;

class AttributeSetManagement implements AttributeSetManagementInterface
{
    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var AttributeSetRepository
     */
    private $repository;

    /**
     * @param Config $eavConfig
     * @param AttributeSetRepository $repository
     */
    public function __construct(
        EavConfig $eavConfig,
        AttributeSetRepository $repository
    ) {
        $this->eavConfig = $eavConfig;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function create($entityTypeCode, AttributeSetInterface $attributeSet, $skeletonId) {
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        if ($attributeSet->getId()) {
            throw InputException::invalidFieldValue('id', $$attributeSet->getId());
        }
        if ($skeletonId == 0) {
            throw InputException::invalidFieldValue('skeletonId', $skeletonId);
        }
        $attributeSet->setEntityTypeId($this->eavConfig->getEntityType($entityTypeCode)->getId());
        $attributeSet->validate();
        $this->repository->save($attributeSet);

        // Make sure that skeleton attribute set is valid (try to load it)
        $this->repository->get($skeletonId);
        $attributeSet->initFromSkeleton($skeletonId);
        return $this->repository->save($attributeSet);
    }
}