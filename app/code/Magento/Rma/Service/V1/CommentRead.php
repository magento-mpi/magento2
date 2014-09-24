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
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResultsBuilder;
use Magento\Rma\Model\Rma\PermissionChecker;
use Magento\Rma\Model\RmaRepository;

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
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var RmaStatusHistorySearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @var RmaRepository
     */
    private $repository;

    /**
     * @param HistoryRepository $historyRepository
     * @param RmaStatusHistoryMapper $historyMapper
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param RmaStatusHistorySearchResultsBuilder $searchResultsBuilder
     * @param RmaRepository $repository
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        HistoryRepository $historyRepository,
        RmaStatusHistoryMapper $historyMapper,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        RmaStatusHistorySearchResultsBuilder $searchResultsBuilder,
        RmaRepository $repository,
        PermissionChecker $permissionChecker
    ) {
        $this->historyRepository = $historyRepository;
        $this->historyMapper = $historyMapper;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->permissionChecker = $permissionChecker;
        $this->repository = $repository;
    }

    /**
     * Comments list
     *
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResults
     */
    public function commentsList($id)
    {
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkRmaForCustomerContext();

        $rmaModel = $this->repository->get($id);

        $filters = [$this->filterBuilder->setField('rma_entity_id')->setValue($rmaModel->getId())->create()];
        if ($this->permissionChecker->isCustomerContext()) {
            $filters[] = $this->filterBuilder->setField('is_visible_on_front')->setValue(1)->create();
        }

        $this->criteriaBuilder->addFilter($filters);

        $criteria = $this->criteriaBuilder->create();
        $comments = [];
        foreach ($this->historyRepository->find($criteria) as $comment) {
            $comments[] = $this->historyMapper->extractDto($comment);
        }
        return $this->searchResultsBuilder->setItems($comments)
            ->setTotalCount(count($comments))
            ->setSearchCriteria($criteria)
            ->create();
    }
}
