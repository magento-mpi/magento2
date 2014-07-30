<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Service\V1\Data\OrderMapper;
use Magento\Sales\Service\V1\Data\OrderSearchResultsBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Class OrderList
 */
class OrderList implements OrderListInterface
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderMapper
     */
    protected $orderMapper;

    /**
     * @var OrderSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderMapper $orderMapper
     * @param OrderSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderMapper $orderMapper,
        OrderSearchResultsBuilder $searchResultsBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderMapper = $orderMapper;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * Invoke OrderList service
     *
     * @param SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\SearchResults
     */
    public function invoke(SearchCriteria $searchCriteria)
    {
        $orders = [];
        foreach($this->orderRepository->find($searchCriteria) as $order) {
            $orders[] = $this->orderMapper->extractDto($order);
        }
        return $this->searchResultsBuilder->setItems($orders)
            ->setSearchCriteria($searchCriteria)
            ->create();
    }
}
