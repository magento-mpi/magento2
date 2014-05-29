<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

use \Magento\Catalog\Service\V1\Product\Data;
use \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory as AttributeGroupCollectionFactory;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory
     */
    protected $groupListFactory;

    /**
     * @var \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroupBuilder
     */
    protected $groupBuilder;

    /**
     * @param AttributeGroupCollectionFactory $groupListFactory
     * @param \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroupBuilder $groupBuilder
     */
    public function __construct(
        AttributeGroupCollectionFactory $groupListFactory,
        Data\Eav\AttributeGroupBuilder $groupBuilder
    ) {
        $this->groupListFactory = $groupListFactory;
        $this->groupBuilder = $groupBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($attributeSetId)
    {
        $collection = $this->groupListFactory->create();
        $collection->setAttributeSetFilter($attributeSetId);
        $collection->setSortOrder();

        $groups = array();

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
