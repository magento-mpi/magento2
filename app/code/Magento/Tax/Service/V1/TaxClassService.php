<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Tax\Model\Converter;
use Magento\Tax\Model\Resource\TaxClass\Collection as TaxClassCollection;
use Magento\Tax\Model\Resource\TaxClass\CollectionFactory as TaxClassCollectionFactory;
use Magento\Tax\Service\V1\Data\SearchResultsBuilder;

/**
 * Tax class service.
 */
class TaxClassService implements TaxClassServiceInterface
{
    /**
     * @var TaxClassCollectionFactory
     */
    protected $taxClassCollectionFactory;

    /**
     * @var SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * Initialize dependencies.
     *
     * @param TaxClassCollectionFactory $taxClassCollectionFactory
     * @param SearchResultsBuilder $searchResultsBuilder
     * @param Converter $converter
     */
    public function __construct(
        TaxClassCollectionFactory $taxClassCollectionFactory,
        SearchResultsBuilder $searchResultsBuilder,
        Converter $converter
    ) {
        $this->taxClassCollectionFactory = $taxClassCollectionFactory;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function searchTaxClass(SearchCriteria $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var TaxClassCollection $collection */
        $collection = $this->taxClassCollectionFactory->create();
        /** TODO: This method duplicates functionality of search methods in other services and should be refactored. */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $field => $direction) {
                $collection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $taxClasses = [];
        /** @var \Magento\Tax\Model\ClassModel $taxClassModel */
        foreach ($collection as $taxClassModel) {
            $taxClasses[] = $this->converter->createTaxClassData($taxClassModel);
        }
        $this->searchResultsBuilder->setItems($taxClasses);
        return $this->searchResultsBuilder->create();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * TODO: This method duplicates functionality of search methods in other services and should be refactored.
     *
     * @param FilterGroup $filterGroup
     * @param TaxClassCollection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, TaxClassCollection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = array('attribute' => $filter->getField(), $condition => $filter->getValue());
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
