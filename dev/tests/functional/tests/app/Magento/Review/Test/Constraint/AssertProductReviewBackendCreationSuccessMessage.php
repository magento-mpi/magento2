<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductReviewBackendCreationSuccessMessage
 * Assert that success message is displayed after review created
 */
class AssertProductReviewBackendCreationSuccessMessage extends AbstractConstraint
{
    /**
     * Text of success message after review created
     */
    const SUCCESS_MESSAGE = 'You saved the review.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that success message is displayed after review created
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
     * Text success create message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Review success create message is present.';
    }
}
