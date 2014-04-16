<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sitemap\Test\Page\Adminhtml\AdminSitemapIndex;
use Magento\Sitemap\Test\Fixture\Sitemap;

/**
 * Class AssertSitemapFailPathSaveMessage
 *
 * @package Magento\Sitemap\Test\Constraint
 */
class AssertSitemapFailPathSaveMessage extends AbstractConstraint
{
    const FAILPATH = 'Path "/%s" is not available and cannot be used.';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that fail message is displayed after wrong sitemap save
     *
     * @param AdminSitemapIndex $sitemapPage
     * @param Sitemap $sitemap
     * @return void
     */
    public function processAssert(AdminSitemapIndex $sitemapPage, Sitemap $sitemap)
    {
        $actualMessage = $sitemapPage->getSitemapSaveMessage()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::FAILPATH, $sitemap->getSitemapFilename()),
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::FAILPATH
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of fail create sitemap assert.
     *
     * @return string
     */
    public function toString()
    {
        return 'Fail message for create sitemap is present.';
    }
}
