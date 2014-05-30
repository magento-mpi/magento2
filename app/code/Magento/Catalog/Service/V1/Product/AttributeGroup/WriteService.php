<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

use \Magento\Catalog\Model\Product\Attribute\GroupFactory;
use \Magento\Catalog\Model\Product\Attribute\Group;
use Magento\Catalog\Service\V1\Data\Eav\AttributeGroupBuilder;
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
     * @var  \Magento\Catalog\Service\V1\Data\Eav\AttributeGroupBuilder
     */
    protected $groupBuilder;

    /**
     * @param GroupFactory $groupFactory
     * @param AttributeGroupBuilder $groupBuilder
     */
    public function __construct(GroupFactory $groupFactory, AttributeGroupBuilder $groupBuilder)
    {
        $this->groupFactory = $groupFactory;
        $this->groupBuilder = $groupBuilder;
    }

    /**
     * {inheritdoc}
     */
    public function create($attributeSetId, \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup $groupData)
    {
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
            throw new CouldNotSaveException('Could not create attribute group');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update($groupId, \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup $groupData)
    {
        /** @var Group $attributeGroup */
        $attributeGroup = $this->groupFactory->create();
        $attributeGroup->load($groupId);
        if (!$attributeGroup->getId()) {
            throw new NoSuchEntityException();
        }
        try {
            $attributeGroup->setId($groupData->getId());
            $attributeGroup->setAttributeGroupName($groupData->getName());
            $attributeGroup->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not update attribute group');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($groupId)
    {
        /** @var Group $attributeGroup */
        $attributeGroup = $this->groupFactory->create();
        $attributeGroup->load($groupId);
        if ($attributeGroup->hasSystemAttributes()) {
            throw new StateException('Attribute group that contains system attributes can not be deleted');
        }
        if (!$attributeGroup->getId()) {
            throw new NoSuchEntityException();
        }
        $attributeGroup->delete();
    }
}
