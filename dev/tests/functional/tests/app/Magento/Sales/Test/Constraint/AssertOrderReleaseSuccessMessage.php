<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderReleaseSuccessMessage
 * Assert release success message is displayed on order index page
 */
class AssertOrderReleaseSuccessMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_RELEASE_MESSAGE = '%d order(s) have been released from on hold status.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert release success message is displayed on order index page
     *
     * @param OrderIndex $orderIndex
     * @param int $ordersCount
     * @return void
     */
    public function processAssert(OrderIndex $orderIndex, $ordersCount)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_RELEASE_MESSAGE, $ordersCount),
            $orderIndex->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Release success message is displayed on order index page.';
    }
}
