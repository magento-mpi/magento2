<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertArchivedOrderCancelMassActionSuccessMessage
 * Assert that success message is displayed on "Archived Orders Grid" page
 */
class AssertArchivedOrderCancelMassActionSuccessMessage extends AbstractConstraint
{
    /**
     * Message displayed after cancel order from archive
     */
    const SUCCESS_MESSAGE = 'We canceled %d order(s).';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed on "Archived Orders Grid" page
     *
     * @param ArchiveOrders $archiveOrders
     * @param int $successMassActions
     * @return void
     */
    public function processAssert(ArchiveOrders $archiveOrders, $successMassActions)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $successMassActions),
            $archiveOrders->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message of canceled orders is present on archived orders grid.';
    }
}
