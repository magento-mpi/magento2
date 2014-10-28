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
     * @var \Magento\Catalog\Service\V1\Data\Product\SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var Entity\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\IdentifierFactory
     */
    protected $attributeIdentifierFactory;

    /**
     * @param Config $eavConfig
     * @param Resource\Entity\Attribute $eavResource
     * @param Resource\Entity\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Catalog\Service\V1\Data\Product\SearchResultsBuilder $searchResultsBuilder
     * @param Entity\AttributeFactory $attributeFactory
     * @param Entity\Attribute\IdentifierFactory $attributeIdentifierFactory
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Resource\Entity\Attribute $eavResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Service\V1\Data\Product\SearchResultsBuilder $searchResultsBuilder,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Entity\Attribute\IdentifierFactory $attributeIdentifierFactory
    ) {
        $this->eavConfig = $eavConfig;
        $this->eavResource = $eavResource;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->attributeFactory = $attributeFactory;
        $this->attributeIdentifierFactory = $attributeIdentifierFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Eav\Api\Data\AttributeInterface $attribute)
    {
        $this->eavResource->save($attribute);
        return $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($entityTypeCode, \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        if (!$entityTypeCode) {
            throw InputException::requiredField('entity_type_code');
        }

        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
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
            $identifier = $this->attributeIdentifierFactory->create([
                'attributeCode' => $attribute->getAttributeCode(),
                'entityTypeCode' => $entityTypeCode
            ]);
            $attributes[] = $this->get($identifier);
        }
        $this->searchResultsBuilder->setItems($attributes);
        $this->searchResultsBuilder->setTotalCount($totalCount);
        return $this->searchResultsBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get(\Magento\Eav\Model\Entity\Attribute\Identifier $identifier)
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
        $attribute = $this->eavConfig->getAttribute($identifier->getEntityTypeCode(), $identifier->getAttributeCode());
        if (!$attribute->getAttributeId()) {
            throw new NoSuchEntityException(sprintf(
                'Attribute with attributeCode "%s" does not exist.',
                $identifier->getAttributeCode()
            ));
        }
        return $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Eav\Api\Data\AttributeInterface $attribute)
    {
        $this->eavResource->delete($attribute);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($attributeId)
    {
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        $attribute = $this->attributeFactory->create();
        $this->eavResource->load($attribute, $attributeId);

        if (!$attribute->getAttributeId()) {
            throw new NoSuchEntityException(sprintf('Attribute with id "%s" does not exist.', $attributeId));
        }

        $this->delete($attribute);
        return true;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Service\V1\Data\Search\FilterGroup $filterGroup
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    private function addFilterGroupToCollection(
        \Magento\Framework\Service\V1\Data\Search\FilterGroup $filterGroup,
        Collection $collection
    ) {
        /** @var \Magento\Framework\Service\V1\Data\Search\FilterGroup $filter */
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter(
                $filter->getField(),
                [$condition => $filter->getValue()]
            );
        }
    }
}
