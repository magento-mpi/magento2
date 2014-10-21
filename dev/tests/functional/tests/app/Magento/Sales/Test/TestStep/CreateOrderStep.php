<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Fixture\OrderInjectable;

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
    }
}
