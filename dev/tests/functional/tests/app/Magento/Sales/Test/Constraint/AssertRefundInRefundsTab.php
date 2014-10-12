<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class AssertRefundInRefundsTab
 * Assert that refund is present in the Credit Memo tab with correct refunded items quantity
 */
class AssertRefundInRefundsTab extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that refund is present in the Credit Memo tab with correct refunded items quantity
     *
     * @param OrderView $orderView
     * @param OrderIndex $orderIndex
     * @param OrderInjectable $order
     * @param array $ids
     * @param array $creditMemo
     * @return void
     */
    public function processAssert(
        OrderView $orderView,
        OrderIndex $orderIndex,
        OrderInjectable $order,
        array $ids,
        array $creditMemo
    ) {
        $orderIndex->open();
        $orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $orderView->getOrderForm()->openTab('creditmemos');

        foreach ($ids['creditMemoIds'] as $creditMemoId) {
            $amount = $creditMemo['qty'];
            $filter = [
                'id' => $creditMemoId,
                'amount_from' => $amount,
                'amount_to' => $amount
            ];
            \PHPUnit_Framework_Assert::assertTrue(
                $orderView->getOrderForm()->getTabElement('creditmemos')->getGridBlock()->isRowVisible($filter),
                'Refund is absent on credit memo tab.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Refund is present on credit memo tab.';
    }
}
