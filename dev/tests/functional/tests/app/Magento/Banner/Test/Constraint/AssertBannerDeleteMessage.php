<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;

/**
 * Class AssertBannerDeleteMessage
 * Assert that success delete message is appeared after banner has been deleted
 */
class AssertBannerDeleteMessage extends AbstractConstraint
{
    const SUCCESS_DELETE_MESSAGE = 'The banner has been deleted.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that success delete message is appeared after banner has been deleted
     *
     * @param BannerIndex $bannerIndex
     * @return void
     */
    public function processAssert(BannerIndex $bannerIndex)
    {
        $actualMessage = $bannerIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong delete message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Delete message is displayed.';
    }
}
