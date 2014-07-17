<?php
/**
 * Created by PhpStorm.
 * User: akaplya
 * Date: 17.07.14
 * Time: 11:35
 */

namespace Magento\Sales\Service;

use Magento\Sales\Service\Data\OrderBuilder;

class OrderConverter
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderBuilder
     */
    protected $orderBuilder;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderBuilder $orderBuilder
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderBuilder $orderBuilder
    ){
        $this->orderRepository = $orderRepository;
        $this->orderBuilder = $orderBuilder;
    }

    public function toModel($dto)
    {
        return ;
    }

    public function toDto($model)
    {
        return ;
    }
}
