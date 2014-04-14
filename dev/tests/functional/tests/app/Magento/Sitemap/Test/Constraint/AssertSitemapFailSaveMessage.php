<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Constraint;

use Magento\Sitemap\Test\Fixture\Sitemap;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sitemap\Test\Page\Adminhtml\AdminSitemapIndex;

/**
 * Class AssertSitemapFailSaveMessage
 *
 * @package Magento\Sitemap\Test\Constraint
 */
class AssertSitemapFailSaveMessage extends AbstractConstraint
{
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
    public function processAssert(
        AdminSitemapIndex $sitemapPage,
        Sitemap $sitemap
    ) {
        $messages = array(
            'Please create the specified folder "' . $sitemap->getSitemapPath() . '" before saving the sitemap.',
            'Path "/' . $sitemap->getSitemapFilename() . '" is not available and cannot be used.'
        );
        $actualMessage = $sitemapPage->getSitemapSaveMessage()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertContains(
            $actualMessage,
            $messages,
            'Wrong success message is displayed.'
            . "\nExpected1: " . $messages[0]
            . "\nExpected2: " . $messages[1]
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        //
    }
}
