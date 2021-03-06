<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Page\Adminhtml\CmsBlockIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsBlockSuccessSaveMessage
 * Assert that after save block successful message appears
 */
class AssertCmsBlockSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    const SUCCESS_SAVE_MESSAGE = 'The block has been saved.';

    /**
     * Assert that after save block successful message appears
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
