<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;

/**
 * Class AssertCmsPageRevisionErrorDeleteMessage
 * Assert that error delete message is displayed on the page
 */
class AssertCmsPageRevisionErrorDeleteMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const ERROR_DELETE_MESSAGE = 'Revision #%d could not be removed because it is published.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that error delete message is displayed on the page
     *
     * @param CmsVersionEdit $cmsVersionEdit
     * @param array $results
     * @return void
     */
    public function processAssert(CmsVersionEdit $cmsVersionEdit, array $results)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::ERROR_DELETE_MESSAGE, $results['quantity']),
            $cmsVersionEdit->getMessagesBlock()->getErrorMessages(),
            'Wrong error message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Page Revision error delete message is present.';
    }
}
