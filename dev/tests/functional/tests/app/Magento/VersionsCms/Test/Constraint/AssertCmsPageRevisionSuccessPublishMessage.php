<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\Cms\Test\Page\AdminHtml\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageRevisionSuccessPublishMessage
 * Assert success publish message is displayed on the page
 */
class AssertCmsPageRevisionSuccessPublishMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Text value to be checked
     */
    const SUCCESS_PUBLISH_MESSAGE = 'You have published the revision.';

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
