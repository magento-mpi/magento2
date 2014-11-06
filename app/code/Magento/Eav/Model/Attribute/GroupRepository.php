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
use \Magento\Framework\Exception\InputException;

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
     * @var \Magento\Eav\Api\Data\AttributeGroupDataBuilder
     */
    protected $groupBuilder;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $setRepository;

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
     * @param \Magento\Eav\Api\Data\AttributeGroupDataBuilder $groupBuilder
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $setRepository
     * @param \Magento\Framework\Data\Search\SearchResultsInterfaceBuilder $searchResultsBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Resource\Entity\Attribute\Group $groupResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $groupListFactory,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $groupFactory,
        \Magento\Eav\Api\Data\AttributeGroupDataBuilder $groupBuilder,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $setRepository,
        \Magento\Framework\Data\Search\SearchResultsInterfaceBuilder $searchResultsBuilder
    ) {
        $this->groupResource = $groupResource;
        $this->groupListFactory = $groupListFactory;
        $this->groupFactory = $groupFactory;
        $this->groupBuilder = $groupBuilder;
        $this->setRepository = $setRepository;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Eav\Api\Data\AttributeGroupInterface $group)
    {
        try {
            $this->setRepository->get($group->getAttributeSetId());
        } catch (NoSuchEntityException $ex) {
            throw NoSuchEntityException::singleField('attributeSetId', $group->getAttributeSetId());
        }

        if ($group->getId()) {
            /** @var \Magento\Eav\Model\Entity\Attribute\Group $existingGroup */
            $existingGroup = $this->groupFactory->create();
            $this->groupResource->load($existingGroup, $group->getId());

            if (!$existingGroup->getId()) {
                throw NoSuchEntityException::singleField('attributeGroupId', $existingGroup->getId());
            }
            if ($existingGroup->getAttributeSetId() != $group->getAttributeSetId()) {
                throw new StateException('Attribute group does not belong to provided attribute set');
            }
        }

        try {
            $this->groupResource->save($group);
        } catch (\Exception $e) {
            throw new StateException('Cannot save attributeGroup');
        }
        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria)
    {
        $attributeSetId = $this->retrieveAttributeSetIdFromSearchCriteria($searchCriteria);
        if (!$attributeSetId) {
            throw InputException::requiredField('attribute_set_id');
        }
        if (!$this->setRepository->get($attributeSetId)->getId()) {
            throw NoSuchEntityException::singleField('attributeSetId', $attributeSetId);
        }

        $collection = $this->groupListFactory->create();
        $collection->setAttributeSetFilter($attributeSetId);
        $collection->setSortOrder();

        $groups = [];
        /** @var $group \Magento\Eav\Model\Entity\Attribute\Group */
        foreach ($collection->getItems() as $group) {
            $this->groupBuilder->setId($group->getId());
            $this->groupBuilder->setName($group->getAttributeGroupName());
            $groups[] = $this->groupBuilder->create();
        }

        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        $this->searchResultsBuilder->setItems($groups);
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        return $this->searchResultsBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($groupId)
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
    public function delete(\Magento\Eav\Api\Data\AttributeGroupInterface $group)
    {
        try {
            $this->groupResource->delete($group);
        } catch (\Exception $e) {
            throw new StateException('Cannot delete attributeGroup with id %attribute_group_id',
                ['attribute_group_id' => $group->getId()], $e);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($groupId)
    {
        $this->delete(
            $this->get($groupId)
        );
        return true;
    }

    /**
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
     * @return null|string
     */
    protected function retrieveAttributeSetIdFromSearchCriteria(
        \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
    ) {
        foreach ($searchCriteria->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == 'attribute_set_id') {
                    return $filter->getValue();
                }
            }
        }
        return null;
    }
}
