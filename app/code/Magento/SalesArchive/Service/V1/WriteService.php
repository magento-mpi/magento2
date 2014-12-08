<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Service\V1;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\SalesArchive\Model\Archive;
use Magento\SalesArchive\Service\V1\Data\ArchiveMapper;
use Magento\SalesArchive\Service\V1\Data\ArchiveSearchResultsBuilder;

class WriteService implements WriteServiceInterface
{
    /**
     * @var Archive
     */
    protected $archive;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var OrderSearchResultInterface
     */
    protected $orderSearchResult;

    /**
     * @var ArchiveMapper
     */
    protected $archiveMapper;

    /**
     * @var ArchiveSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * Sales archive config
     *
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $salesArchiveConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param Archive $archive
     * @param OrderSearchResultInterface $orderSearchResult
     * @param ArchiveMapper $archiveMapper
     * @param ArchiveSearchResultsBuilder $searchResultsBuilder
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param \Magento\SalesArchive\Model\Config $salesArchiveConfig
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        Archive $archive,
        OrderSearchResultInterface $orderSearchResult,
        ArchiveMapper $archiveMapper,
        ArchiveSearchResultsBuilder $searchResultsBuilder,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        \Magento\SalesArchive\Model\Config $salesArchiveConfig,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        $this->archive = $archive;
        $this->orderSearchResult = $orderSearchResult;
        $this->archiveMapper = $archiveMapper;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->salesArchiveConfig = $salesArchiveConfig;
        $this->dateTime = $dateTime;
    }

    /**
     * Move orders to archive grid service
     *
     * @return bool
     */
    public function moveOrdersToArchive()
    {
        return (bool)$this->archive->archiveOrders();
    }

    /**
     * Remove order from archive grid by id service
     *
     * @param int $id
     * @return bool
     */
    public function removeOrderFromArchiveById($id)
    {
        return (bool)$this->archive->removeOrdersFromArchiveById($id);
    }

    /**
     * Retrieve archived orders grid service
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\SalesArchive\Service\V1\Data\ArchiveSearchResults
     */
    public function getList(SearchCriteria $searchCriteria)
    {
        $statuses = $this->salesArchiveConfig->getArchiveOrderStatuses();
        if (empty($statuses)) {
            return $this->searchResultsBuilder->create();
        }
        $filters = [$this->filterBuilder->setField('status')->setValue($statuses)->setConditionType('in')->create()];
        $archiveAge = $this->salesArchiveConfig->getArchiveAge();
        if ((int)$archiveAge) {
            $date = date_create($this->dateTime->formatDate(true));
            date_add($date, date_interval_create_from_date_string((int)$archiveAge . ' days'));
            $filters[] = $this->filterBuilder->setField('updated_at')
                ->setValue($date->format('Y-m-d'))
                ->setConditionType('lteq')->create();
        }
        $this->criteriaBuilder->addFilter($filters);
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->criteriaBuilder->addFilter($group->getFilters());
        }
        $criteria = $this->criteriaBuilder->create();
        $orders = [];
        foreach ($this->orderSearchResult->getItems() as $order) {
            $orders[] = $this->archiveMapper->extractDto($order);
        }
        return $this->searchResultsBuilder->setItems($orders)
            ->setTotalCount(count($orders))
            ->setSearchCriteria($criteria)
            ->create();
    }
}
