<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\Rma\Model\Rma\Status\HistoryRepository;
use Magento\Rma\Service\V1\Data\RmaStatusHistoryMapper;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResultsBuilder;

class CommentRead implements CommentReadInterface
{
    /**
     * @var HistoryRepository
     */
    protected $historyRepository;

    /**
     * @var RmaStatusHistoryMapper
     */
    protected $historyMapper;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var RmaStatusHistorySearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param HistoryRepository $historyRepository
     * @param RmaStatusHistoryMapper $historyMapper
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param RmaStatusHistorySearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        HistoryRepository $historyRepository,
        RmaStatusHistoryMapper $historyMapper,
        SearchCriteriaBuilder $criteriaBuilder,
        RmaStatusHistorySearchResultsBuilder $searchResultsBuilder
    ) {
        $this->historyRepository = $historyRepository;
        $this->historyMapper = $historyMapper;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * Comments list
     *
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResults
     */
    public function commentsList($id)
    {
        $criteria = $this->criteriaBuilder->create();
        $comments = [];
        foreach ($this->historyRepository->find($criteria) as $comment) {
            $comments[] = $this->historyMapper->extractDto($comment);
        }
        return $this->searchResultsBuilder->setItems($comments)
            ->setTotalCount(count($comments))
            ->create();
    }
}
