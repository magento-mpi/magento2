<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Review\Test\Page\Adminhtml\ReviewIndex;

/**
 * Class AssertProductReviewMassActionSuccessMessage
 * Assert success message appears after updated via massactions
 */
class AssertProductReviewMassActionSuccessMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'A total of 1 record(s) have been updated.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that success message is displayed after updated via massactions
     *
     * @param ReviewIndex $reviewIndex
     * @return void
     */
    public function processAssert(ReviewIndex $reviewIndex)
    {
        $actualMessage = $reviewIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
        );
    }

    /**
     * Text success save message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Review success message appears after updated via massactions is present.';
    }
}
