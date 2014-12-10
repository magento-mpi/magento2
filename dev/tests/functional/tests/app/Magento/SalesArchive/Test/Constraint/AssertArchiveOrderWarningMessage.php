<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertArchiveOrderWarningMessage
 * Assert that warning message present on order grid page
 */
class AssertArchiveOrderWarningMessage extends AbstractConstraint
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
