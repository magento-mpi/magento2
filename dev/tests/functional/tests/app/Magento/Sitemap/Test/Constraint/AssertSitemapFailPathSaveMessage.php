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
 * Class AssertSitemapFailPathSaveMessage
 *
 * @package Magento\Sitemap\Test\Constraint
 */
class AssertSitemapFailPathSaveMessage extends AbstractConstraint
{
    const FAIL_PATH_MESSAGE = 'Path "/%s" is not available and cannot be used.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that error message is displayed after creating sitemap with wrong path
     *
     * @param SitemapIndex $sitemapPage
     * @param Sitemap $sitemap
     * @return void
     */
    public function processAssert(SitemapIndex $sitemapPage, Sitemap $sitemap)
    {
        $actualMessage = $sitemapPage->getMessagesBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::FAIL_PATH_MESSAGE, $sitemap->getSitemapFilename()),
            $actualMessage,
            'Wrong error message is displayed.'
            . "\nExpected: " . self::FAIL_PATH_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Error message after creating sitemap with wrong path is present.';
    }
}
