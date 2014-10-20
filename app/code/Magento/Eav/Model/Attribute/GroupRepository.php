<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Attribute;

use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\StateException;

class GroupRepository implements \Magento\Eav\Api\AttributeGroupRepositoryInterface
{
    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group
     */
    protected $groupResource;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\GroupBuilder
     */
    protected $groupBuilder;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $setFactory;

    /**
     * @var \Magento\Framework\Data\Search\SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory
     */
    protected $groupListFactory;

    /**
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group $groupResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $groupListFactory
     * @param \Magento\Eav\Model\Entity\Attribute\GroupFactory $groupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\GroupBuilder $groupBuilder
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     * @param \Magento\Framework\Data\Search\SearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Resource\Entity\Attribute\Group $groupResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $groupListFactory,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $groupFactory,
        \Magento\Eav\Model\Entity\Attribute\GroupBuilder $groupBuilder,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Framework\Data\Search\SearchResultsBuilder $searchResultsBuilder
    ) {
        $this->groupResource = $groupResource;
        $this->groupListFactory = $groupListFactory;
        $this->groupFactory = $groupFactory;
        $this->groupBuilder = $groupBuilder;
        $this->setFactory = $setFactory;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Eav\Api\Data\AttributeGroupInterface $group, array $arguments = [])
    {
        if (!$this->setFactory->create()->load($group->getAttributeSetId())->getId()) {
            throw NoSuchEntityException::singleField('attributeSetId', $group->getAttributeSetId());
        }

        if ($group->getId()) {
            /** @var \Magento\Eav\Model\Entity\Attribute\Group $group */
            $existingGroup = $this->groupFactory->create();
            $this->groupResource->load($existingGroup, $group->getId());

            if (!$existingGroup->getId()) {
                throw NoSuchEntityException::singleField('attributeGroupId', $existingGroup->getId());
            }
            if ($existingGroup->getAttributeSetId() != $group->getAttributeSetId()) {
                throw new StateException('Attribute group does not belong to provided attribute set');
            }
        }

        $this->groupBuilder->setId($group->getId());
        $this->groupBuilder->setAttributeGroupName($group->getName());
        $this->groupBuilder->setAttributeSetId($group->getAttributeSetId());

        $this->groupResource->save($this->groupBuilder->create());
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria,
        array $arguments = []
    ) {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        $attributeSetId = null;
        foreach ($searchCriteria->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == 'attribute_set_id') {
                    $attributeSetId = $filter->getValue();
                    break 2;
                }
            }
        }

        if (!$this->setFactory->create()->load($attributeSetId)->getId()) {
            throw NoSuchEntityException::singleField('attributeSetId', $attributeSetId);
        }

        $collection = $this->groupListFactory->create();
        $collection->setAttributeSetFilter($attributeSetId);
        $collection->setSortOrder();

        $groups = array();

        /** @var $group \Magento\Eav\Model\Entity\Attribute\Group */
        foreach ($collection->getItems() as $group) {
            $this->groupBuilder->setId($group->getId());
            $this->groupBuilder->setName($group->getAttributeGroupName());
            $groups[] = $this->groupBuilder->create();
        }

        $this->searchResultsBuilder->setItems($groups);
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        return $this->searchResultsBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($groupId, array $arguments = [])
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\Group $group */
        $group = $this->groupFactory->create();
        $this->groupResource->load($group, $groupId);
        if (!$group->getId()) {
            throw new NoSuchEntityException(sprintf('Group with id "%s" does not exist.', $groupId));
        }
        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Eav\Api\Data\AttributeGroupInterface $group, array $arguments = [])
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\Group $group */
        $attributeGroup = $this->groupFactory->create();
        $this->groupResource->load($group, $group->getId());
        /**
         * todo: in catalog service in this method used additional validation - "hasSystemAttributes" from model \Magento\Catalog\Model\Product\Attribute\Group
         * todo: need to resolve this validation
         */
        if (!$attributeGroup->getId()) {
            throw NoSuchEntityException::singleField('attributeGroupId', $group->getId());
        }
        if ($attributeGroup->getAttributeSetId() != $group->getAttributeSetId()) {
            throw new StateException('Attribute group does not belong to provided attribute set');
        }
        $this->groupResource->delete($attributeGroup);
        return true;
    }
}
