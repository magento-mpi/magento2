<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

use Magento\Catalog\Service\V1\Data;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory as AttributeGroupCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory
     */
    protected $groupListFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Eav\AttributeGroupBuilder
     */
    protected $groupBuilder;

    /**
     * @param AttributeGroupCollectionFactory $groupListFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param Data\Eav\AttributeGroupBuilder $groupBuilder
     */
    public function __construct(
        AttributeGroupCollectionFactory $groupListFactory,
        AttributeSetFactory $attributeSetFactory,
        Data\Eav\AttributeGroupBuilder $groupBuilder
    ) {
        $this->groupListFactory = $groupListFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->groupBuilder = $groupBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($attributeSetId)
    {
        if (!$this->attributeSetFactory->create()->load($attributeSetId)->getId()) {
            throw NoSuchEntityException::singleField('attributeSetId', $attributeSetId);
        }

        $collection = $this->groupListFactory->create();
        $collection->setAttributeSetFilter($attributeSetId);
        $collection->setSortOrder();

        $groups = [];

        /** @var $group \Magento\Eav\Model\Entity\Attribute\Group */
        foreach ($collection->getItems() as $group) {
            $this->groupBuilder->setId(
                $group->getId()
            )->setName(
                $group->getAttributeGroupName()
            );
            $groups[] = $this->groupBuilder->create();
        }
        return $groups;
    }
}
