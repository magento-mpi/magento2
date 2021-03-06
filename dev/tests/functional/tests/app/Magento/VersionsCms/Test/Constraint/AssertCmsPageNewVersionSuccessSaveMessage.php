<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageNewVersionSuccessSaveMessage
 * Assert that after new CMS page version save successful message appears
 */
class AssertCmsPageNewVersionSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * New version success save message
     */
    const SUCCESS_SAVE_MESSAGE = 'You have created the new version.';

    /**
     * Assert that after new CMS page version save successful message appears
     *
     * @param CmsVersionEdit $cmsVersionEdit
     * @return void
     */
    public function processAssert(CmsVersionEdit $cmsVersionEdit)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $cmsVersionEdit->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return '"You have created the new version" success save message is present.';
    }
}
