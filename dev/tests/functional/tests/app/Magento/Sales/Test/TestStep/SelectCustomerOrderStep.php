<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Mtf\TestStep\TestStepInterface;

/**
 * Class SelectCustomerOrderStep
 * Select Customer for Order
 */
class SelectCustomerOrderStep implements TestStepInterface
{
    /**
     * Sales order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Customer
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param CustomerInjectable $customer
     */
    public function __construct(OrderCreateIndex $orderCreateIndex, CustomerInjectable $customer)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->customer = $customer;
    }

    /**
     * Select Customer for Order
     *
     * @return void
     */
    public function run()
    {
        $this->orderCreateIndex->getCustomerBlock()->selectCustomer($this->customer);
    }
}
