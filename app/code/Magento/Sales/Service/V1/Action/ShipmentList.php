<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Service\V1\Data\ShipmentMapper;
use Magento\Sales\Service\V1\Data\ShipmentSearchResultsBuilder;
use Magento\Framework\Api\SearchCriteria;

/**
 * Class ShipmentList
 */
class ShipmentList
{
    /**
     * @var ShipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @var ShipmentMapper
     */
    protected $shipmentMapper;

    /**
     * @var ShipmentSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param ShipmentRepository $shipmentRepository
     * @param ShipmentMapper $shipmentMapper
     * @param ShipmentSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        ShipmentRepository $shipmentRepository,
        ShipmentMapper $shipmentMapper,
        ShipmentSearchResultsBuilder $searchResultsBuilder
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentMapper = $shipmentMapper;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * Invoke ShipmentList service
     *
     * @param SearchCriteria $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function invoke(SearchCriteria $searchCriteria)
    {
        $shipments = [];
        foreach ($this->shipmentRepository->find($searchCriteria) as $shipment) {
            $shipments[] = $this->shipmentMapper->extractDto($shipment);
        }
        return $this->searchResultsBuilder->setItems($shipments)
            ->setTotalCount(count($shipments))
            ->setSearchCriteria($searchCriteria)
            ->create();
    }
}
