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
use Magento\Sitemap\Test\Page\Adminhtml\AdminSitemapIndex;
use Magento\Sitemap\Test\Page\Adminhtml\SitemapNewIndex;


/**
 * Cover creating SitemapEntity
 *
 * Test Flow:
 *  1. Log in as  admin user from data set.
 *  2. Navigate to Marketing &gt; SEO and Search &gt; Site Map.
 *  3. Click "Add Sitemap" button.
 *  4. Fill out all data according to data set.
 *  5. Click "Save" button.
 *  6. Perform all assertions.
 *
 * @group XML_Sitemap_(MX)
 * @ZephyrId MTA-147
 */
class CreatingSitemapEntityTest extends Injectable
{
    /**
     * @var AdminSitemapIndex
     */
    protected $adminSitemapIndex;

    /**
     * @var SitemapNewIndex
     */
    protected $sitemapNewIndex;

    /**
     * @param AdminSitemapIndex $adminSitemapIndex
     * @param SitemapNewIndex $sitemapNewIndex
     */
    public function __inject(
        AdminSitemapIndex $adminSitemapIndex,
        SitemapNewIndex $sitemapNewIndex
    ) {
        $this->adminSitemapIndex = $adminSitemapIndex;
        $this->sitemapNewIndex = $sitemapNewIndex;
    }

    /**
     * @param Sitemap $sitemap
     */
    public function testCreateSitemap(Sitemap $sitemap)
    {
        $this->adminSitemapIndex->open();
        $this->adminSitemapIndex->getGridPageActions()->addNew();
        $this->sitemapNewIndex->getSitemapForm()->fill($sitemap);
        $this->sitemapNewIndex->getFormPageActions()->save();
    }
}
