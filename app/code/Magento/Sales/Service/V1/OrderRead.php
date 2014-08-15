<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Action\OrderGet;
use Magento\Sales\Service\V1\Action\OrderList;
use Magento\Sales\Service\V1\Action\OrderCommentsList;
use Magento\Sales\Service\V1\Action\OrderGetStatus;
use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Class OrderRead
 */
class OrderRead implements OrderReadInterface
{
    /**
     * @var OrderGet
     */
    protected $orderGet;

    /**
     * @var OrderList
     */
    protected $orderList;

    /**
     * @var OrderCommentsList
     */
    protected $orderCommentsList;

    /**
     * @var OrderGetStatus
     */
    protected $orderGetStatus;

    /**
     * @param OrderGet $orderGet
     * @param OrderList $orderList
     * @param OrderCommentsList $orderCommentsList
     * @param OrderGetStatus $orderGetStatus
     */
    public function __construct(
        OrderGet $orderGet,
        OrderList $orderList,
        OrderCommentsList $orderCommentsList,
        OrderGetStatus $orderGetStatus
    ) {
        $this->orderGet = $orderGet;
        $this->orderList = $orderList;
        $this->orderCommentsList = $orderCommentsList;
        $this->orderGetStatus = $orderGetStatus;
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        return $this->orderGet->invoke($id);
    }

    /**
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Service\V1\Data\OrderSearchResults
     */
    public function search(SearchCriteria $searchCriteria)
    {
        return $this->orderList->invoke($searchCriteria);
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\OrderStatusHistorySearchResults
     */
    public function commentsList($id)
    {
        return $this->orderCommentsList->invoke($id);
    }

    /**
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStatus($id)
    {
        return $this->orderGetStatus->invoke($id);
    }
}
