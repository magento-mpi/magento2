<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockIndex;

/**
 * Class AssertCmsBlockDeleteMessage
 * Assert that after delete CMS block "The block has been deleted." successful message appears
 */
class AssertCmsBlockDeleteMessage extends AbstractConstraint
{
    const SUCCESS_DELETE_MESSAGE = 'The block has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after delete CMS block "The block has been deleted." successful message appears
     *
     * @param CmsBlockIndex $cmsBlockIndex
     * @return void
     */
    public function processAssert(CmsBlockIndex $cmsBlockIndex)
    {
        $actualMessage = $cmsBlockIndex->getMessagesBlock()->getSuccessMessages();
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
        return 'CMS Block success delete message is present.';
    }
}
