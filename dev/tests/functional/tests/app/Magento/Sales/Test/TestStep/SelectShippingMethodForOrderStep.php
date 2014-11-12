<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;

/**
 * Class SelectShippingMethodForOrderStep
 * Select Shipping data
 */
class SelectShippingMethodForOrderStep implements TestStepInterface
{
    /**
     * Sales order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Shipping
     *
     * @var array
     */
    protected $shipping;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param array $shipping
     */
    public function __construct(OrderCreateIndex $orderCreateIndex, array $shipping)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->shipping = $shipping;
    }

    /**
     * Fill Shipping Data
     *
     * @return array
     */
    public function run()
    {
        if ($this->shipping['shipping_service'] != '-') {
            $this->orderCreateIndex->getCreateBlock()->selectShippingMethod($this->shipping);
        }

        return ['shipping' => $this->shipping];
    }
}
