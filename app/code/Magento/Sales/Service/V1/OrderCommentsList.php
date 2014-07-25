<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\Order\Status\HistoryRepository;
use Magento\Sales\Service\V1\Data\OrderStatusHistoryMapper;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Sales\Service\V1\Data\OrderSearchResultsBuilder;

/**
 * Class OrderCommentsList
 */
class OrderCommentsList implements OrderCommentsListInterface
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
     * @var OrderSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param HistoryRepository $historyRepository
     * @param OrderStatusHistoryMapper $historyMapper
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param OrderSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        HistoryRepository $historyRepository,
        OrderStatusHistoryMapper $historyMapper,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        OrderSearchResultsBuilder $searchResultsBuilder
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
     * @return array
     */
    public function invoke($id)
    {
        $this->criteriaBuilder->addFilter(
            ['eq' => $this->filterBuilder->setField('parent_id')->setValue($id)->create()]
        );
        $criteria = $this->criteriaBuilder->create();
        $comments = [];
        foreach($this->historyRepository->find($criteria) as $comment) {
            $comments[] = $this->historyMapper->extractDto($comment);
        }
        return $this->searchResultsBuilder->setItems($comments)
            ->setSearchCriteria($criteria)
            ->create();
    }
}
