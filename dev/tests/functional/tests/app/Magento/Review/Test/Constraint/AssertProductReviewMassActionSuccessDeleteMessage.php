<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Review\Test\Constraint;

use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductReviewMassActionSuccessDeleteMessage
 * Assert success message appears after deletion via mass actions
 */
class AssertProductReviewMassActionSuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Message that appears after deletion via mass actions
     */
    const SUCCESS_DELETE_MESSAGE = 'A total of %d record(s) have been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that success message is displayed after deletion via mass actions
     *
     * @param ReviewInjectable|ReviewInjectable[] $review
     * @param ReviewIndex $reviewIndex
     * @return void
     */
    public function processAssert(ReviewInjectable $review, ReviewIndex $reviewIndex)
    {
        $reviews = is_array($review) ? $review : [$review];
        $deleteMessage = sprintf(self::SUCCESS_DELETE_MESSAGE, count($reviews));
        \PHPUnit_Framework_Assert::assertEquals(
            $deleteMessage,
            $reviewIndex->getMessagesBlock()->getSuccessMessages(),
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
        return 'Review success message appears after deletion via mass actions is present.';
    }
}
