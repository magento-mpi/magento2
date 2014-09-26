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
 * Class AssertArchivedOrderCancelMassActionErrorMessage
 * Assert that error message is displayed on "Archived Orders Grid" page
 */
class AssertArchivedOrderCancelMassActionErrorMessage extends AbstractConstraint
{
    /**
     * Message displayed after unsuccessful orders canceling
     */
    const ERROR_MESSAGE = 'You cannot cancel the order(s).';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that error message is displayed on "Archived Orders Grid" page
     *
     * @param ArchiveOrders $archiveOrders
     * @return void
     */
    public function processAssert(ArchiveOrders $archiveOrders)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_MESSAGE,
            $archiveOrders->getMessagesBlock()->getErrorMessages(),
            'Wrong message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Error message of unsuccessful canceled orders is present on archived orders grid.';
    }
}
