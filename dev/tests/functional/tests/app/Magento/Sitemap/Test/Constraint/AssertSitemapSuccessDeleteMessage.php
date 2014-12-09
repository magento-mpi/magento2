<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sitemap\Test\Page\Adminhtml\SitemapIndex;

/**
 * Class AssertSitemapSuccessDeleteMessage
 */
class AssertSitemapSuccessDeleteMessage extends AbstractConstraint
{
    const SUCCESS_DELETE_MESSAGE = 'The sitemap has been deleted.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that success message is displayed after sitemap delete
     *
     * @param SitemapIndex $sitemapPage
     * @return void
     */
    public function processAssert(SitemapIndex $sitemapPage)
    {
        $actualMessage = $sitemapPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of success delete sitemap assert.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sitemap success delete message is present.';
    }
}
