<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category;

use Magento\Catalog\Api\CategoryAttributeRepositoryInterface;
use Magento\Framework\Data\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Catalog\Api\Data\CategoryAttributeInterface;
use Magento\Framework\Data\Search\FilterGroupInterface;
use Magento\Eav\Model\Resource\Entity\Attribute\Collection as AttributeCollection;

class AttributeRepository implements CategoryAttributeRepositoryInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Data\Search\SearchResultsBuilderInterface
     */
    protected $searchResultsBuilder;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \Magento\Framework\Data\Search\SortOrderInterface
     */
    protected $sortOrder;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Data\Search\SearchResultsBuilderInterface $searchResultsBuilder,
        \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Framework\Data\Search\SortOrderInterface $sortOrder
    ) {
        $this->eavConfig = $eavConfig;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->sortOrder = $sortOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria)
    {
        $categoryEntityType = CategoryAttributeInterface::ENTITY_TYPE_CODE;

        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->join(
            array('entity_type' => $attributeCollection->getTable('eav_entity_type')),
            'main_table.entity_type_id = entity_type.entity_type_id',
            []
        );
        $attributeCollection->addFieldToFilter('entity_type_code', ['eq' => $categoryEntityType]);
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

        $this->searchResultsBuilder->setItems($attributeCollection->getItems());
        $this->searchResultsBuilder->setTotalCount($totalCount);
        return $this->searchResultsBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeCode)
    {
        $categoryEntityType = CategoryAttributeInterface::ENTITY_TYPE_CODE;
        /** @var AbstractAttribute $attribute */
        $attribute = $this->eavConfig->getAttribute($categoryEntityType, $attributeCode);
        if (!$attribute) {
            throw (new NoSuchEntityException('entityType', array($categoryEntityType)))
                ->singleField('attributeCode', $attributeCode);
        }
        return $attribute;
    }

    /**
     * Add FilterGroup to collection
     *
     * @param FilterGroupInterface $filterGroup
     * @param AttributeCollection $collection
     */
    protected function addFilterGroupToCollection(FilterGroupInterface $filterGroup, AttributeCollection $collection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter(
                $filter->getField(),
                [$condition => $filter->getValue()]
            );
        }
    }
}
