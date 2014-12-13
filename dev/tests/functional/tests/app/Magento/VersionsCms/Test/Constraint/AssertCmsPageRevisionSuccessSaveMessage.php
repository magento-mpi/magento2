<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageRevisionSuccessSaveMessage
 * Assert that success save message is displayed on the CmsVersionEdit page
 */
class AssertCmsPageRevisionSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Text value to be checked
     */
    const SUCCESS_SAVE_MESSAGE = 'You have saved the revision.';

    /**
     * Assert that success save message is displayed on the CmsVersionEdit page
     *
     * @param CmsVersionEdit $cmsVersionEdit
     * @return void
     */
    public function processAssert(CmsVersionEdit $cmsVersionEdit)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $cmsVersionEdit->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Success save message is present on CmsVersionEdit page.';
    }
}
