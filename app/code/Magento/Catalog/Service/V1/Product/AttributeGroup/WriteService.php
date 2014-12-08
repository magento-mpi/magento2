<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

use Magento\Catalog\Model\Product\Attribute\Group;
use Magento\Catalog\Model\Product\Attribute\GroupFactory;
use Magento\Catalog\Service\V1\Data\Eav\AttributeGroupBuilder;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Eav\AttributeGroupBuilder
     */
    protected $groupBuilder;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $setFactory;

    /**
     * @param GroupFactory $groupFactory
     * @param SetFactory $attributeSetFactory
     * @param AttributeGroupBuilder $groupBuilder
     */
    public function __construct(
        GroupFactory $groupFactory,
        SetFactory $attributeSetFactory,
        AttributeGroupBuilder $groupBuilder
    ) {
        $this->groupFactory = $groupFactory;
        $this->setFactory = $attributeSetFactory;
        $this->groupBuilder = $groupBuilder;
    }

    /**
     * {inheritdoc}
     */
    public function create($attributeSetId, \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup $groupData)
    {
        if (!$this->setFactory->create()->load($attributeSetId)->getId()) {
            throw NoSuchEntityException::singleField('attributeSetId', $attributeSetId);
        }

        try {
            /** @var Group $attributeGroup */
            $attributeGroup = $this->groupFactory->create();
            $attributeGroup->setAttributeGroupName($groupData->getName());
            $attributeGroup->setAttributeSetId($attributeSetId);
            $attributeGroup->save();
            return $this->groupBuilder->setId(
                $attributeGroup->getId()
            )->setName(
                $attributeGroup->getAttributeGroupName()
            )->create();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                'Could not create attribute group. Maybe group with such name already exists'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update($attributeSetId, $groupId, \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup $groupData)
    {
        /** @var Group $attributeGroup */
        $attributeGroup = $this->groupFactory->create();
        $attributeGroup->load($groupId);
        if (!$attributeGroup->getId()) {
            throw NoSuchEntityException::singleField('attributeGroupId', $attributeGroup->getId());
        }
        if ($attributeGroup->getAttributeSetId() != $attributeSetId) {
            throw new StateException('Attribute group does not belong to provided attribute set');
        }
        try {
            $attributeGroup->setId($groupId);
            $attributeGroup->setAttributeGroupName($groupData->getName());
            $attributeGroup->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not update attribute group');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($attributeSetId, $groupId)
    {
        /** @var Group $attributeGroup */
        $attributeGroup = $this->groupFactory->create();
        $attributeGroup->load($groupId);

        if (!$attributeGroup->getId()) {
            throw NoSuchEntityException::singleField('attributeGroupId', $groupId);
        }
        if ($attributeGroup->hasSystemAttributes()) {
            throw new StateException('Attribute group that contains system attributes can not be deleted');
        }
        if ($attributeGroup->getAttributeSetId() != $attributeSetId) {
            throw new StateException('Attribute group does not belong to provided attribute set');
        }
        $attributeGroup->delete();
        return true;
    }
}
