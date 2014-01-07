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

use Magento\Customer\Service\Entity\V1\CustomerGroup;
use Magento\Customer\Service\Entity\V1\Filter;
use Magento\Customer\Service\Entity\V1\SearchCriteria;

class ServiceCollection extends \Magento\Data\Collection
{
    /**
     * @var \Magento\Customer\Service\CustomerGroupV1Interface
     */
    protected $groupService;

    /**
     * @var \Magento\Customer\Service\Entity\V1\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Customer\Service\Entity\V1\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Customer\Service\CustomerGroupV1Interface $groupService
     * @param \Magento\Customer\Service\Entity\V1\FilterBuilder $filterBuilder
     * @param \Magento\Customer\Service\Entity\V1\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Customer\Service\CustomerGroupV1Interface $groupService,
        \Magento\Customer\Service\Entity\V1\FilterBuilder $filterBuilder,
        \Magento\Customer\Service\Entity\V1\SearchCriteriaBuilder $searchCriteriaBuilder
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

    public function addFieldToFilter($field, $condition)
    {
        // TODO this is broken until the Widget/Grid can be re-written not to have db logic in it
        return $this;
    }

    protected function getSearchCriteria()
    {
        foreach ($this->_filters as $filter) {
            $filerBuilder->setField($filter['field'])
                ->setValue($filter['value'])
                ->setConditionType($filter['type']);
            $this->searchCriteriaBuilder->addFilter($filterBuilder->create());
        }
        foreach ($this->_orders as $field => $direction) {
            $this->searchCriteriaBuilder->addSortOrder(
                $field, $direction == 'ASC' ? SearchCriteria::SORT_ASC : SearchCriteria::SORT_DESC);
        }
        $this->searchCriteriaBuilder->setCurrentPage($this->_curPage);
        $this->searchCriteriaBuilder->setPageSize($this->_pageSize);
        return $this->searchCriteriaBuilder->create();
    }
}
