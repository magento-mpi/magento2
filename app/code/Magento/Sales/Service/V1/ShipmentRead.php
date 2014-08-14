<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Action\ShipmentGet;
use Magento\Sales\Service\V1\Action\ShipmentList;
use Magento\Sales\Service\V1\Action\ShipmentCommentsList;
use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Class ShipmentRead
 */
class ShipmentRead implements ShipmentReadInterface
{
    /**
     * @var ShipmentGet
     */
    protected $shipmentGet;

    /**
     * @var ShipmentList
     */
    protected $shipmentList;

    /**
     * @var ShipmentCommentsList
     */
    protected $shipmentCommentsList;

    /**
     * @param ShipmentGet $shipmentGet
     * @param ShipmentList $shipmentList
     * @param ShipmentCommentsList $shipmentCommentsList
     */
    public function __construct(
        ShipmentGet $shipmentGet,
        ShipmentList $shipmentList,
        ShipmentCommentsList $shipmentCommentsList
    ) {
        $this->shipmentGet = $shipmentGet;
        $this->shipmentList = $shipmentList;
        $this->shipmentCommentsList = $shipmentCommentsList;
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Shipment
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        return $this->shipmentGet->invoke($id);
    }

    /**
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Service\V1\Data\SearchResults
     */
    public function search(SearchCriteria $searchCriteria)
    {
        return $this->shipmentList->invoke($searchCriteria);
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResults
     */
    public function commentsList($id)
    {
        return $this->shipmentCommentsList->invoke($id);
    }
}
