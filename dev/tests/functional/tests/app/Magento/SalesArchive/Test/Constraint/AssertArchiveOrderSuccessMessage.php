<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertArchiveOrderSuccessMessage
 * Assert that success message is displayed on "Orders Grid" page
 */
class AssertArchiveOrderSuccessMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Message displayed after moving order to archive
     */
    const SUCCESS_MESSAGE = 'We archived %d order(s).';

    /**
     * Assert that success message is displayed on "Orders Grid" page
     *
     * @param OrderIndex $orderIndex
     * @param string $number
     * @return void
     */
    public function processAssert(OrderIndex $orderIndex, $number)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $number),
            $orderIndex->getMessagesBlock()->getSuccessMessages(),
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
        return 'Order success move to archive message is present.';
    }
}
