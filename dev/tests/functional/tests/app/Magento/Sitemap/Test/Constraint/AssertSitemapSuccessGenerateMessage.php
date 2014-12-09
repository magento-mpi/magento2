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
use Magento\Sitemap\Test\Fixture\Sitemap;

/**
 * Class AssertSitemapSuccessGenerateMessage
 */
class AssertSitemapSuccessGenerateMessage extends AbstractConstraint
{
    const SUCCESS_GENERATE_MESSAGE = 'The sitemap "%s" has been generated.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that success message is displayed after sitemap generate
     *
     * @param SitemapIndex $sitemapPage
     * @param Sitemap $sitemap
     * @return void
     */
    public function processAssert(
        SitemapIndex $sitemapPage,
        Sitemap $sitemap
    ) {
        $actualMessage = $sitemapPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_GENERATE_MESSAGE, $sitemap->getSitemapFilename()),
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . sprintf(self::SUCCESS_GENERATE_MESSAGE, $sitemap->getSitemapFilename())
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of success create sitemap assert.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sitemap success generate message is present.';
    }
}
