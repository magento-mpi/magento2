<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageVersionSuccessSaveMessage
 * Assert that success save message is displayed on the page
 */
class AssertCmsPageVersionSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'You have saved the version.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that success save message is displayed on the page
     *
     * @param CmsNew $cmsNew
     * @return void
     */
    public function processAssert(CmsNew $cmsNew)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $cmsNew->getMessagesBlock()->getSuccessMessages(),
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
        return 'CMS Page Version success save message is present.';
    }
}
