<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\Status\HistoryRepository;
use Magento\Sales\Service\V1\Data\OrderStatusHistoryMapper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Service\V1\Data\OrderStatusHistorySearchResultsBuilder;

/**
 * Class OrderCommentsList
 */
class OrderCommentsList
{
    /**
     * @var HistoryRepository
     */
    protected $historyRepository;

    /**
     * @var OrderStatusHistoryMapper
     */
    protected $historyMapper;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var OrderStatusHistorySearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param HistoryRepository $historyRepository
     * @param OrderStatusHistoryMapper $historyMapper
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param OrderStatusHistorySearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        HistoryRepository $historyRepository,
        OrderStatusHistoryMapper $historyMapper,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        OrderStatusHistorySearchResultsBuilder $searchResultsBuilder
    ) {
        $this->historyRepository = $historyRepository;
        $this->historyMapper = $historyMapper;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * Invoke OrderCommentsList service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\OrderStatusHistorySearchResults
     */
    public function invoke($id)
    {
        $this->criteriaBuilder->addFilter(
            ['eq' => $this->filterBuilder->setField('parent_id')->setValue($id)->create()]
        );
        $criteria = $this->criteriaBuilder->create();
        $comments = [];
        foreach ($this->historyRepository->find($criteria) as $comment) {
            $comments[] = $this->historyMapper->extractDto($comment);
        }
        return $this->searchResultsBuilder->setItems($comments)
            ->setSearchCriteria($criteria)
            ->setTotalCount(count($comments))
            ->create();
    }
}
