<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\TestCase;

use Magento\Sitemap\Test\Fixture\Sitemap;
use Mtf\TestCase\Injectable;
use Magento\Sitemap\Test\Page\Adminhtml\SitemapIndex;
use Magento\Sitemap\Test\Page\Adminhtml\SitemapNew;

/**
 * Cover creating SitemapEntity
 *
 * Test Flow:
 *  1. Log in as admin user from data set.
 *  2. Navigate to Marketing > SEO and Search > Site Map.
 *  3. Click "Add Sitemap" button.
 *  4. Fill out all data according to data set.
 *  5. Click "Save" button.
 *  6. Perform all assertions.
 *
 * @group XML_Sitemap_(MX)
 * @ZephyrId MAGETWO-23277
 */
class CreateSitemapEntityTest extends Injectable
{
    /**
     * @var SitemapIndex
     */
    protected $adminSitemapIndex;

    /**
     * @var SitemapNew
     */
    protected $sitemapNewIndex;

    /**
     * @param SitemapIndex $adminSitemapIndex
     * @param SitemapNew $sitemapNewIndex
     */
    public function __inject(
        SitemapIndex $adminSitemapIndex,
        SitemapNew $sitemapNewIndex
    ) {
        $this->adminSitemapIndex = $adminSitemapIndex;
        $this->sitemapNewIndex = $sitemapNewIndex;
    }

    /**
     * @param Sitemap $sitemap
     */
    public function testCreateSitemap(Sitemap $sitemap)
    {
        //Steps
        $this->adminSitemapIndex->open();
        $this->adminSitemapIndex->getGridPageActions()->addNew();
        $this->sitemapNewIndex->getSitemapForm()->fill($sitemap);
        $this->sitemapNewIndex->getFormPageActions()->save();
    }
}
