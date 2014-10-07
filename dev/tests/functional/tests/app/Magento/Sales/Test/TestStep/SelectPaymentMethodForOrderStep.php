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
 * Class SelectPaymentMethodForOrderStep
 * Fill Payment Data Step
 */
class SelectPaymentMethodForOrderStep implements TestStepInterface
{
    /**
     * Sales order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Payment
     *
     * @var array
     */
    protected $payment;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param array $payment
     */
    public function __construct(OrderCreateIndex $orderCreateIndex, array $payment)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->payment = $payment;
    }

    /**
     * Fill Payment data
     *
     * @return void
     */
    public function run()
    {
        $this->orderCreateIndex->getCreateBlock()->selectPaymentMethod($this->payment);
    }
}
