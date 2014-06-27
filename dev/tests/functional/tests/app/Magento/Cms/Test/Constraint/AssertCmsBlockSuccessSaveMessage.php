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
 * Class AssertCmsBlockSuccessSaveMessage
 */
class AssertCmsBlockSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'The block has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after save block  "The block has been saved." successful message appears
     *
     * @param CmsBlockIndex $cmsBlockIndex
     * @return void
     */
    public function processAssert(CmsBlockIndex $cmsBlockIndex)
    {
        $actualMessage = $cmsBlockIndex->getMessagesBlock()->getSuccessMessages();
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
        return 'CMS Block success create message is present.';
    }
}
