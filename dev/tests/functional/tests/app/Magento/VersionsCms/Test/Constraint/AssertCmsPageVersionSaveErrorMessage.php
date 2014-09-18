<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageVersionSaveErrorMessage
 * Assert that after change access level of last public version to private error message appears
 */
class AssertCmsPageVersionSaveErrorMessage extends AbstractConstraint
{
    const ERROR_SAVE_MESSAGE = 'Сannot change version access level because it is the last public version for its page.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

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
        return '"Сannot change version access level because it is the last public version for its page"'
        . 'error message is present.';
    }
}
