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
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Catalog\Service\V1\Data\Product\SearchResultsBuilder;

/**
 * Class OrderList
 */
class OrderList
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
     * @var SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderMapper $orderMapper
     * @param SearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderMapper $orderMapper,
        SearchResultsBuilder $searchResultsBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderMapper = $orderMapper;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * Invoke OrderList service
     *
     * @param SearchCriteria $criteria
     * @return \Magento\Catalog\Service\V1\Data\Product\SearchResults
     */
    public function invoke(SearchCriteria $criteria)
    {
        $orders = [];
        foreach($this->orderRepository->find($criteria) as $order) {
            $orders[] = $this->orderMapper->extractDto($order);
        }
        return $this->searchResultsBuilder->setItems($orders)
            ->setSearchCriteria($criteria)
            ->create();
    }
}
