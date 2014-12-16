<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageDuplicateErrorMessage
 * Verify that page has not been created
 */
class AssertCmsPageDuplicateErrorMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const ERROR_SAVE_MESSAGE = 'A page URL key for specified store already exists.';

    /**
     * Verify that page has not been created
     *
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex)
    {
        $message = $cmsIndex->getMessagesBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_SAVE_MESSAGE,
            $message,
            'Wrong error message is displayed.'
            . "\nExpected: " . self::ERROR_SAVE_MESSAGE
            . "\nActual: " . $message
        );
    }

    /**
     * Page with duplicated identifier has not been created
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that page with duplicated identifier has not been created.';
    }
}
