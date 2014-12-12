<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageSuccessSaveMessage
 * Assert that after save a CMS page "The page has been saved." successful message appears
 */
class AssertCmsPageSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'The page has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after save a CMS page "The page has been saved." successful message appears
     *
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex)
    {
        $actualMessage = $cmsIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Success message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message is displayed.';
    }
}
