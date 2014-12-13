<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageRevisionSuccessMassDeleteMessage
 * Assert that success mass delete message is displayed on the page
 */
class AssertCmsPageRevisionSuccessMassDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'medium';
    /* end tags */

    /**
     * Text value to be checked
     */
    const SUCCESS_MASS_DELETE_MESSAGE = 'A total of %d record(s) have been deleted.';

    /**
     * Assert that success mass delete message is displayed on the page
     *
     * @param CmsVersionEdit $cmsVersionEdit
     * @param array $results
     * @return void
     */
    public function processAssert(CmsVersionEdit $cmsVersionEdit, array $results)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MASS_DELETE_MESSAGE, $results['quantity']),
            $cmsVersionEdit->getMessagesBlock()->getSuccessMessages(),
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
        return 'CMS Page Revision success mass delete message is present.';
    }
}
