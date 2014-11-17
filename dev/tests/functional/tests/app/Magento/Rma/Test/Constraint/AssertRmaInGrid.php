<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Rma\Test\Page\Adminhtml\RmaIndex;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Assert that return request displayed in Returns grid.
 */
class AssertRmaInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that return request displayed in Returns grid:
     * - customer
     * - status (pending)
     * - orderID
     *
     * @param Rma $rma
     * @param RmaIndex $rmaIndex
     * @return void
     */
    public function processAssert(Rma $rma, RmaIndex $rmaIndex)
    {
        /** @var OrderInjectable $order*/
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        /** @var CustomerInjectable $customer */
        $customer = $order->getDataFieldConfig('customer_id')['source']->getCustomer();
        $orderId = $rma->getOrderId();
        $filter = [
            'order_id_from' => $orderId,
            'order_id_to' => $orderId,
            'customer' => sprintf('%s %s', $customer->getFirstname(), $customer->getLastname()),
            'status' => $rma->getStatus()
        ];

        $rmaIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $rmaIndex->getRmaGrid()->isRowVisible($filter),
            'Rma for order' . $orderId . ' is absent in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Rma is present in grid';
    }
}
