<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\CommentMapper;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Sales\Model\Order\Creditmemo\CommentRepository;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder;

/**
 * Class CreditmemoCommentsList
 */
class CreditmemoCommentsList
{
    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\CommentRepository
     */
    protected $commentRepository;

    /**
     * @var \Magento\Sales\Service\V1\Data\CommentMapper
     */
    protected $commentMapper;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo\CommentRepository $commentRepository
     * @param \Magento\Sales\Service\V1\Data\CommentMapper $commentMapper
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     * @param \Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder $searchResultsBuilder
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
     * Invoke CreditmemoCommentsList service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResults
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
