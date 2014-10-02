<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveCreditMemos;

/**
 * Class AssertArchiveCreditMemoInGrid
 * Refund with corresponding fixture data is present in Archive Credit Memos grid
 */
class AssertArchiveCreditMemoInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Refund with corresponding fixture data is present in Archive Credit Memos grid
     *
     * @param ArchiveCreditMemos $archiveCreditMemos
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(ArchiveCreditMemos $archiveCreditMemos, OrderInjectable $order, array $ids)
    {
        $orderId = $order->getId();
        $archiveCreditMemos->open();

        foreach ($ids['creditMemoIds'] as $creditMemoId) {
            $filter = [
                'order_id' => $orderId,
                'creditmemo_id' => $creditMemoId
            ];

            $errorMessage = implode(', ', $filter);
            \PHPUnit_Framework_Assert::assertTrue(
                $archiveCreditMemos->getCreditMemosGrid()->isRowVisible($filter),
                'Credit memo with following data \'' . $errorMessage . '\' is absent in archive credit memos grid.'
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
        return 'Credit memo is present in archive credit memos grid.';
    }
}
