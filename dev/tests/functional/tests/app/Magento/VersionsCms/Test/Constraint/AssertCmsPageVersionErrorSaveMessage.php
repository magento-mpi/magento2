<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageVersionErrorSaveMessage
 * Assert that after change access level of last public version to private error message appears
 */
class AssertCmsPageVersionErrorSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Save error message
     */
    const ERROR_SAVE_MESSAGE = 'Cannot change version access level because it is the last public version for its page.';

    /**
     * Assert that after change access level of last public version to private error message appears
     *
     * @param CmsVersionEdit $cmsVersionEdit
     * @return void
     */
    public function processAssert(CmsVersionEdit $cmsVersionEdit)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_SAVE_MESSAGE,
            $cmsVersionEdit->getMessagesBlock()->getErrorMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return '"' . self::ERROR_SAVE_MESSAGE . '" error message is present.';
    }
}
