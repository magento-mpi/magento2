<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageDeleteMessage
 */
class AssertCmsPageDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const SUCCESS_DELETE_MESSAGE = 'The page has been deleted.';

    /**
     * Assert that success message is displayed after Cms page delete
     *
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex)
    {
        $actualMessage = $cmsIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
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
        return 'Cms page success delete message is present.';
    }
}
