<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\CommentMapper;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Model\Order\Shipment\CommentRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder;

/**
 * Class ShipmentCommentsList
 */
class ShipmentCommentsList
{
    /**
     * @var \Magento\Sales\Model\Order\Shipment\CommentRepository
     */
    protected $commentRepository;

    /**
     * @var \Magento\Sales\Service\V1\Data\CommentMapper
     */
    protected $commentMapper;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param \Magento\Sales\Model\Order\Shipment\CommentRepository $commentRepository
     * @param \Magento\Sales\Service\V1\Data\CommentMapper $commentMapper
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
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
     * Invoke ShipmentCommentsList service
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
