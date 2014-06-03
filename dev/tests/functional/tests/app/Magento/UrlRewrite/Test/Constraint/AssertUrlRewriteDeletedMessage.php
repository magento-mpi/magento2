<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Constraint;

use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertUrlRewriteDeletedMessage
 * Assert that delete message is displayed
 */
class AssertUrlRewriteDeletedMessage extends AbstractConstraint
{
    /**
     * Message that displayed after delete url rewrite
     */
    const SUCCESS_DELETE_MESSAGE = 'The URL Rewrite has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that delete message is displayed
     *
     * @param UrlrewriteIndex $index
     * @return void
     */
    public function processAssert(UrlrewriteIndex $index)
    {
        $actualMessage = $index->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Url rewrite delete message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Url rewrite delete message is displayed.';
    }
}
