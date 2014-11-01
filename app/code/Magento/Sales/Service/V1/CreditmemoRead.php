<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Action\CreditmemoGet;
use Magento\Sales\Service\V1\Action\CreditmemoList;
use Magento\Sales\Service\V1\Action\CreditmemoCommentsList;
use Magento\Framework\Api\SearchCriteria;

/**
 * Class CreditmemoRead
 */
class CreditmemoRead implements CreditmemoReadInterface
{
    /**
     * @var CreditmemoGet
     */
    protected $creditmemoGet;

    /**
     * @var CreditmemoList
     */
    protected $creditmemoList;

    /**
     * @var CreditmemoCommentsList
     */
    protected $creditmemoCommentsList;

    /**
     * @param CreditmemoGet $creditmemoGet
     * @param CreditmemoList $creditmemoList
     * @param CreditmemoCommentsList $creditmemoCommentsList
     */
    public function __construct(
        CreditmemoGet $creditmemoGet,
        CreditmemoList $creditmemoList,
        CreditmemoCommentsList $creditmemoCommentsList
    ) {
        $this->creditmemoGet = $creditmemoGet;
        $this->creditmemoList = $creditmemoList;
        $this->creditmemoCommentsList = $creditmemoCommentsList;
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Creditmemo
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        return $this->creditmemoGet->invoke($id);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function search(SearchCriteria $searchCriteria)
    {
        return $this->creditmemoList->invoke($searchCriteria);
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResults
     */
    public function commentsList($id)
    {
        return $this->creditmemoCommentsList->invoke($id);
    }
}
