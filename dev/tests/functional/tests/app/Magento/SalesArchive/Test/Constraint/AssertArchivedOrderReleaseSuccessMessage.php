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
 * Class AssertArchivedOrderReleaseSuccessMessage
 */
class AssertArchivedOrderReleaseSuccessMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_MESSAGE = '%d order(s) have been released from on hold status.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert release success message is displayed on archive order index page
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
        return 'Release success message is displayed on archive order index page.';
    }
}
