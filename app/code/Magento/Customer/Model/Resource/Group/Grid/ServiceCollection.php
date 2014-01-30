<?php
/**
 * Customer group collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource\Group\Grid;

use Magento\Customer\Service\V1\Dto\CustomerGroup;
use Magento\Customer\Service\V1\Dto\Filter;
use Magento\Customer\Service\V1\Dto\SearchCriteria;

class ServiceCollection extends \Magento\Data\Collection
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $groupService;

    /**
     * @var \Magento\Customer\Service\V1\Dto\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Customer\Service\V1\Dto\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Customer\Service\V1\Dto\FilterBuilder $filterBuilder
     * @param \Magento\Customer\Service\V1\Dto\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Customer\Service\V1\Dto\FilterBuilder $filterBuilder,
        \Magento\Customer\Service\V1\Dto\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($entityFactory);
        $this->groupService = $groupService;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Load customer group collection data from service
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return  \Magento\Data\Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $searchCriteria = $this->getSearchCriteria();
            $searchResults = $this->groupService->searchGroups($searchCriteria);
            $this->_totalRecords = $searchResults->getTotalCount();
            /** @var CustomerGroup[] $groups */
            $groups = $searchResults->getItems();
            foreach ($groups as $group) {
                $groupItem = new \Magento\Object();
                $groupItem->addData($group->__toArray());
                $this->_addItem($groupItem);
            }
            $this->_setIsLoaded();
        }
        return $this;
    }

    private function addField($field, $condition)
    {
        $this->filterBuilder->setField($field);

        if (is_array($condition)) {
            $this->filterBuilder->setValue(reset($condition));
            $this->filterBuilder->setConditionType(key($condition));
        } else {
            // not an array, just use eq as condition type and given value
            $this->filterBuilder->setConditionType('eq');
            $this->filterBuilder->setValue($condition);
        }
        $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
    }

    protected function getSearchCriteria()
    {
        foreach ($this->_fieldFilters as $filter) {
            // just one field, move this to a function and call in loop if multiple
            if (!is_array($filter['field'])) {
                $this->addField($filter['field'], $filter['condition']);
            } else {
                // array of fields
                foreach ($filter['field'] as $index => $field) {
                    $this->addField($field, $filter['condition'][$index]);
                }
            }
        }
        foreach ($this->_orders as $field => $direction) {
            $this->searchCriteriaBuilder->addSortOrder(
                $field,
                $direction == 'ASC' ? SearchCriteria::SORT_ASC : SearchCriteria::SORT_DESC
            );
        }
        $this->searchCriteriaBuilder->setCurrentPage($this->_curPage);
        $this->searchCriteriaBuilder->setPageSize($this->_pageSize);
        return $this->searchCriteriaBuilder->create();
    }
}
