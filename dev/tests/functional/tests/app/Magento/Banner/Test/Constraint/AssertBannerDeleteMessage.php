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
 * Assert that after banner delete "The banner has been deleted." successful message appears
 */
class AssertBannerDeleteMessage extends AbstractConstraint
{
    const DELETE_MESSAGE = 'The banner has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after banner delete "The banner has been deleted." successful message appears
     *
     * @param BannerIndex $bannerIndex
     * @return void
     */
    public function processAssert(BannerIndex $bannerIndex)
    {
        $actualMessage = $bannerIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::DELETE_MESSAGE,
            $actualMessage,
            'Wrong delete message is displayed.'
            . "\nExpected: " . self::DELETE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Delete message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Delete message is displayed.';
    }
}
