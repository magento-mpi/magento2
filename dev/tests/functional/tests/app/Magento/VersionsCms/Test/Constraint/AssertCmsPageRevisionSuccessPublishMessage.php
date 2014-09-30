<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\AdminHtml\CmsIndex;

/**
 * Class AssertCmsPageRevisionSuccessPublishMessage
 * Assert success publish message is displayed on the page
 */
class AssertCmsPageRevisionSuccessPublishMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_PUBLISH_MESSAGE = 'You have published the revision.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert success publish message is displayed on the page
     *
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_PUBLISH_MESSAGE,
            $cmsIndex->getMessagesBlock()->getSuccessMessages(),
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
        return 'CMS Page Revision success publish message is present.';
    }
}
