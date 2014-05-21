<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\UrlrewriteIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertUrlRewriteSaveMessage
 * Assert that url rewrite success message is displayed
 */
class AssertUrlRewriteSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'The URL Rewrite has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that url rewrite success message is displayed
     *
     * @param UrlrewriteIndex $index
     * @return void
     */
    public function processAssert(UrlrewriteIndex $index)
    {
        $actualMessage = $index->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Url rewrite success message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Url rewrite success message is displayed.';
    }
}
