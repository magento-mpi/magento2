<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\TestStep;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\TestStep\TestStepInterface;

/**
 * Class CreateOrderStep
 * Step for create order
 */
class CreateOrderStep implements TestStepInterface
{
    /**
     * Order
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param OrderInjectable $order
     */
    public function __construct(OrderInjectable $order)
    {
        $this->order = $order;
    }

    /**
     * Create order
     *
     * @return array
     */
    public function run()
    {
        $this->order->persist();

        return ['products' => $this->order->getEntityId()['products'], 'order' => $this->order];
    }
}
