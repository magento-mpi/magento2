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
 * Class AssertRemoveOrdersFromArchiveSuccessMessage
 * Assert that after removed orders success message presents
 */
class AssertArchiveOrderSuccessRemoveMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_MESSAGE = 'We removed %d order(s) from the archive.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after removed orders success message presents
     *
     * @param ArchiveOrders $archiveOrder
     * @param int $successMassActions
     * @return void
     */
    public function processAssert(ArchiveOrders $archiveOrder, $successMassActions)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $successMassActions),
            $archiveOrder->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Removed orders from archive success message is displayed.';
    }
}
