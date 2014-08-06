<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\Order\Invoice\CommentRepository;
use Magento\Sales\Service\V1\Data\CommentMapper;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder;

/**
 * Class InvoiceCommentsList
 */
class InvoiceCommentsList implements InvoiceCommentsListInterface
{
    /**
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     * @var CommentMapper
     */
    protected $commentMapper;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var CommentSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param CommentRepository $commentRepository
     * @param CommentMapper $commentMapper
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param CommentSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        CommentRepository $commentRepository,
        CommentMapper $commentMapper,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        CommentSearchResultsBuilder $searchResultsBuilder
    ) {
        $this->commentRepository = $commentRepository;
        $this->commentMapper = $commentMapper;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * Invoke InvoiceCommentsList service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder
     */
    public function invoke($id)
    {
        $this->criteriaBuilder->addFilter(
            ['eq' => $this->filterBuilder->setField('parent_id')->setValue($id)->create()]
        );
        $criteria = $this->criteriaBuilder->create();
        $comments = [];
        foreach ($this->commentRepository->find($criteria) as $comment) {
            $comments[] = $this->commentMapper->extractDto($comment);
        }
        return $this->searchResultsBuilder->setItems($comments)
            ->setSearchCriteria($criteria)
            ->setTotalCount(count($comments))
            ->create();
    }
}
