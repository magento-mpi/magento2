<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class AssertOrderReleaseFailMessage
 * Assert release fail message is displayed on order index page
 */
class AssertOrderReleaseFailMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const FAIL_RELEASE_MESSAGE = 'No order(s) were released from on hold status.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert release fail message is displayed on order index page
     *
     * @param OrderIndex $orderIndex
     * @return void
     */
    public function processAssert(OrderIndex $orderIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::FAIL_RELEASE_MESSAGE,
            $orderIndex->getMessagesBlock()->getErrorMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Release fail message is displayed on order index page.';
    }
}
