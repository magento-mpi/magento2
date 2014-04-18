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
use Magento\Sitemap\Test\Page\Adminhtml\SitemapEdit;

/**
 * Cover deleting Sitemap Entity
 *
 * Test Flow:
 *  1. Log in as admin user from data set.
 *  2. Navigate to Marketing > SEO and Search > Site Map.
 *  3. Open sitemap from precondition.
 *  4. Click "Delete" button.
 *  5. Perform all assertions.
 *
 * @group XML_Sitemap_(MX)
 * @ZephyrId MAGETWO-23296
 */

class DeleteSitemapEntityTest extends Injectable
{
    /**
     * @var SitemapIndex
     */
    protected $adminSitemapIndex;

    /**
     * @var SitemapEdit
     */
    protected $sitemapEdit;

    /**
     * @param SitemapIndex $adminSitemapIndex
     * @param SitemapEdit $sitemapEdit
     */
    public function __inject(
        SitemapIndex $adminSitemapIndex,
        SitemapEdit $sitemapEdit
    )
    {
        $this->adminSitemapIndex = $adminSitemapIndex;
        $this->sitemapEdit = $sitemapEdit;
    }

    /**
     * @param Sitemap $sitemap
     */
    public function testDeleteSitemap(Sitemap $sitemap)
    {
        $sitemap->persist();

        $filter = [
            'sitemap_filename' => $sitemap->getSitemapFilename(),
            'sitemap_path' => $sitemap->getSitemapPath(),
            'sitemap_id' => $sitemap->getSitemapId()
        ];

        //Steps
        $this->adminSitemapIndex->open();
        $this->adminSitemapIndex->getSitemapGrid()->searchAndOpen($filter);
        $this->sitemapEdit->getSitemapPageActions()->delete();
    }
}
