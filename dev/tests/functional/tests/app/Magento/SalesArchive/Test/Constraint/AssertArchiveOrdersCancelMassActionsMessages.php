<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertArchiveOrdersCancelMassActionsMessages
 * Assert that error and success messages are displayed on "Archived Orders Grid" page
 */
class AssertArchiveOrdersCancelMassActionsMessages extends AbstractConstraint
{
    /**
     * Message displayed after cancel order from archive
     */
    const SUCCESS_MESSAGE = 'We canceled %d order(s).';

    /**
     * Message displayed after unsuccessful orders canceling
     */
    const ERROR_MESSAGE = '%d order(s) cannot be canceled.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that error and success messages are displayed on "Archived Orders Grid" page
     *
     * @param ArchiveOrders $archiveOrders
     * @param int $successMassActions
     * @param int $ordersQty
     * @return void
     */
    public function processAssert(ArchiveOrders $archiveOrders, $successMassActions, $ordersQty)
    {
        $expectedMessages = [
            sprintf(self::SUCCESS_MESSAGE, $successMassActions),
            sprintf(self::ERROR_MESSAGE, $ordersQty - $successMassActions),
        ];
        $actualMessages = [
            $archiveOrders->getMessagesBlock()->getSuccessMessages(),
            $archiveOrders->getMessagesBlock()->getErrorMessages(),
        ];
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedMessages,
            $actualMessages,
            'Wrong messages are displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Success and error messages are present on archived orders grid.';
    }
}
