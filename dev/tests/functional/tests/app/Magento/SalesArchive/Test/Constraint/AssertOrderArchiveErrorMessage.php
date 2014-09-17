<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class AssertOrderArchiveErrorMessage
 * Assert that warning message present on order grid page
 */
class AssertOrderArchiveErrorMessage extends AbstractConstraint
{
    /**
     * Message displayed after cancel sales order
     */
    const WARNING_MESSAGE = "We can't archive the selected order(s).";

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that warning message present on order grid page
     *
     * @param OrderIndex $orderIndex
     * @return void
     */
    public function processAssert(OrderIndex $orderIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::WARNING_MESSAGE),
            $orderIndex->getMessagesBlock()->getWarningMessages(),
            'Wrong warning message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Warning message that order can\'t be archived is present.';
    }
}
