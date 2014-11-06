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
use Magento\Sales\Service\V1\Action\ShipmentLabelGet;
use Magento\Framework\Api\SearchCriteria;

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
     * @var ShipmentLabelGet
     */
    protected $shipmentLabelGet;

    /**
     * @param ShipmentGet $shipmentGet
     * @param ShipmentList $shipmentList
     * @param ShipmentCommentsList $shipmentCommentsList
     * @param ShipmentLabelGet $shipmentLabelGet
     */
    public function __construct(
        ShipmentGet $shipmentGet,
        ShipmentList $shipmentList,
        ShipmentCommentsList $shipmentCommentsList,
        ShipmentLabelGet $shipmentLabelGet
    ) {
        $this->shipmentGet = $shipmentGet;
        $this->shipmentList = $shipmentList;
        $this->shipmentCommentsList = $shipmentCommentsList;
        $this->shipmentLabelGet = $shipmentLabelGet;
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
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
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

    /**
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLabel($id)
    {
        return $this->shipmentLabelGet->invoke($id);
    }
}
