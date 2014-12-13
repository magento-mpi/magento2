<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageVersionSuccessMassDeleteMessage
 * Assert that success delete message is displayed on the page
 */
class AssertCmsPageVersionSuccessMassDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'medium';
    /* end tags */

    /**
     * Text value to be checked
     */
    const SUCCESS_DELETE_MESSAGE = 'A total of %d record(s) have been deleted.';

    /**
     * Assert that success delete message is displayed on the page
     *
     * @param CmsNew $cmsNew
     * @param array $results
     * @return void
     */
    public function processAssert(CmsNew $cmsNew, array $results)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_DELETE_MESSAGE, $results['quantity']),
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
        return 'CMS Page Version success delete message is present.';
    }
}
