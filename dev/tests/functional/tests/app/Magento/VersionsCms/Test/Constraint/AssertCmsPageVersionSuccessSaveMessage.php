<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsNew;

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
        $actualMessage = $cmsNew->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
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
        return 'CMS Page Version success save message is present.';
    }
}
