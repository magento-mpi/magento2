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
 * Class AssertArchiveOrderReleaseErrorMessage
 * Assert release error message is displayed on archived order index page
 */
class AssertArchiveOrderReleaseErrorMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const ERROR_MESSAGE = 'No order(s) were released from on hold status.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert release error message is displayed on archived order index page
     *
     * @param ArchiveOrders $archiveOrder
     * @return void
     */
    public function processAssert(ArchiveOrders $archiveOrder)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_MESSAGE,
            $archiveOrder->getMessagesBlock()->getErrorMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Release error message is displayed on archived order index page.';
    }
}
