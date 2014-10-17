<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model;

use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Data\Search\SearchCriteriaInterface;
use \Magento\Framework\Data\Search\FilterGroupInterface;
use \Magento\Eav\Model\Resource\Entity\Attribute\Collection;

class AttributeRepository implements \Magento\Eav\Api\AttributeRepositoryInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute
     */
    protected $eavResource;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \Magento\Framework\Data\Search\SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Resource\Entity\Attribute $eavResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Framework\Data\Search\SearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Resource\Entity\Attribute $eavResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Framework\Data\Search\SearchResultsBuilder $searchResultsBuilder
    ) {
        $this->eavConfig = $eavConfig;
        $this->eavResource = $eavResource;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Eav\Api\Data\AttributeInterface $attribute, array $arguments = [])
    {
        $this->eavResource->save($attribute);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria,
        array $arguments = []
    ) {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $entityTypeCode = null;

        foreach ($searchCriteria->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == 'entity_type_code') {
                    $entityTypeCode = $filter->getValue();
                    break 2;
                }
            }
        }

        if (!$entityTypeCode) {
            throw InputException::requiredField('entity_type_code');
        }

        $attributeCollection->addFieldToFilter('entity_type_code', ['eq' => $entityTypeCode]);
        $attributeCollection->join(
            array('entity_type' => $attributeCollection->getTable('eav_entity_type')),
            'main_table.entity_type_id = entity_type.entity_type_id',
            []
        );
        $attributeCollection->join(
            ['eav_entity_attribute' => $attributeCollection->getTable('eav_entity_attribute')],
            'main_table.attribute_id = eav_entity_attribute.attribute_id',
            []
        );
        $attributeCollection->join(
            array('additional_table' => $attributeCollection->getTable('catalog_eav_attribute')),
            'main_table.attribute_id = additional_table.attribute_id',
            []
        );
        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $attributeCollection);
        }
        /** @var \Magento\Framework\Data\Search\SortOrderInterface $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $attributeCollection->addOrder(
                $sortOrder->getField(),
                ($sortOrder->getDirection() == SearchCriteriaInterface::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $totalCount = $attributeCollection->getSize();

        // Group attributes by id to prevent duplicates with different attribute sets
        $attributeCollection->addAttributeGrouping();

        $attributeCollection->setCurPage($searchCriteria->getCurrentPage());
        $attributeCollection->setPageSize($searchCriteria->getPageSize());

        $attributes = [];
        /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
        foreach ($attributeCollection as $attribute) {
            $attributes[] = $this->get($entityTypeCode, $attribute->getAttributeCode());
        }
        $this->searchResultsBuilder->setItems($attributes);
        $this->searchResultsBuilder->setTotalCount($totalCount);
        return $this->searchResultsBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get(\Magento\Eav\Api\Data\AttributeIdentifierInterface $identifier, array $arguments = [])
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
        $attribute = $this->eavConfig->getAttribute($identifier->getEntityTypeCode(), $identifier->getAttributeCode());
        if ($attribute->getAttributeId()) {
            return $attribute;
        }
        $entityException = new NoSuchEntityException('entityType', [$identifier->getEntityTypeCode()]);
        throw $entityException->singleField('attributeCode', $identifier->getAttributeCode());
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Eav\Api\Data\AttributeInterface $attribute, array $arguments = [])
    {
        $this->eavResource->delete($attribute);
        return true;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroupInterface  $filterGroup
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    private function addFilterGroupToCollection(FilterGroupInterface $filterGroup, Collection $collection)
    {
        /** @var \Magento\Framework\Data\Search\FilterInterface $filter */
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter(
                $filter->getField(),
                [$condition => $filter->getValue()]
            );
        }
    }
}
