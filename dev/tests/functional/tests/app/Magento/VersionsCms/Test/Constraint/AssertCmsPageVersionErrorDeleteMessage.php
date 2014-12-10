<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageVersionErrorDeleteMessage
 * Assert that error delete message is displayed on the page
 */
class AssertCmsPageVersionErrorDeleteMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const ERROR_DELETE_MESSAGE = 'Version "%s" cannot be removed because its revision is published.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'medium';

    /**
     * Assert that error delete message is displayed on the page
     *
     * @param CmsNew $cmsNew
     * @param array $results
     * @return void
     */
    public function processAssert(CmsNew $cmsNew, array $results)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::ERROR_DELETE_MESSAGE, $results['label']),
            $cmsNew->getMessagesBlock()->getErrorMessages(),
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
        return 'CMS Page Version success delete message is present.';
    }
}
