<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource\Group\Grid;

use Magento\Core\Model\EntityFactory;
use Magento\Framework\Api\AbstractServiceCollection;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Customer group collection backed by services
 */
class ServiceCollection extends AbstractServiceCollection
{
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SimpleDataObjectConverter
     */
    protected $simpleDataObjectConverter;

    /**
     * @param EntityFactory $entityFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param GroupRepositoryInterface $groupRepository
     * @param SimpleDataObjectConverter $simpleDataObjectConverter
     */
    public function __construct(
        EntityFactory $entityFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        GroupRepositoryInterface $groupRepository,
        SimpleDataObjectConverter $simpleDataObjectConverter
    ) {
        parent::__construct($entityFactory, $filterBuilder, $searchCriteriaBuilder, $sortOrderBuilder);
        $this->groupRepository = $groupRepository;
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
    }

    /**
     * Load customer group collection data from service
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $searchCriteria = $this->getSearchCriteria();
            $searchResults = $this->groupRepository->getList($searchCriteria);
            $this->_totalRecords = $searchResults->getTotalCount();
            /** @var CustomerGroup[] $groups */
            $groups = $searchResults->getItems();
            foreach ($groups as $group) {
                $groupItem = new \Magento\Framework\Object();
                $groupItem->addData($this->simpleDataObjectConverter->toFlatArray($group));
                $this->_addItem($groupItem);
            }
            $this->_setIsLoaded();
        }
        return $this;
    }
}
