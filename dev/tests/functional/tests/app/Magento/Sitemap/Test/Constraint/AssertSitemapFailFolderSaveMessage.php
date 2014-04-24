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
 * Class AssertSitemapFailFolderSaveMessage
 *
 * @package Magento\Sitemap\Test\Constraint
 */
class AssertSitemapFailFolderSaveMessage extends AbstractConstraint
{
    const FAIL_FOLDER_MESSAGE = 'Please create the specified folder "%s" before saving the sitemap.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that wrong success message is displayed after wrong sitemap save
     *
     * @param SitemapIndex $sitemapPage
     * @param Sitemap $sitemap
     * @return void
     */
    public function processAssert(SitemapIndex $sitemapPage, Sitemap $sitemap)
    {
        $actualMessage = $sitemapPage->getMessagesBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::FAIL_FOLDER_MESSAGE, $sitemap->getSitemapPath()),
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::FAIL_FOLDER_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of wrong success message create sitemap assert.
     *
     * @return string
     */
    public function toString()
    {
        return 'Wrong success message for create sitemap is present.';
    }
}
