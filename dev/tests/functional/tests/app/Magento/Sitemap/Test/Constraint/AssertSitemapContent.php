<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Sitemap\Test\Fixture\Sitemap;
use Magento\Sitemap\Test\Page\Adminhtml\SitemapIndex;

/**
 * Class AssertSitemapContent
 */
class AssertSitemapContent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that sitemap.xml file contains correct content according to dataset:
     *  - product url
     *  - category url
     *  - CMS page url
     *
     * @param CatalogProductSimple $product
     * @param CatalogCategory $catalog
     * @param CmsPage $cmsPage
     * @param Sitemap $sitemap
     * @param SitemapIndex $sitemapIndex
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CatalogCategory $catalog,
        CmsPage $cmsPage,
        Sitemap $sitemap,
        SitemapIndex $sitemapIndex
    ) {
        $sitemapIndex->open()->getSitemapGrid()->sortGridByField('sitemap_id');
        $sitemapId = $sitemapIndex->getSitemapGrid()->getSitemapId();
        $filter = [
            'sitemap_filename' => $sitemap->getSitemapFilename(),
            'sitemap_path' => $sitemap->getSitemapPath(),
            'sitemap_id' => $sitemapId
        ];
        $sitemapIndex->getSitemapGrid()->search($filter);
        $content = file_get_contents($sitemapIndex->getSitemapGrid()->getLinkForGoogle());
        $productUrl = $_ENV['app_frontend_url'] . $product->getUrlKey() . '.html';
        $catalogUrl = $_ENV['app_frontend_url'] . $catalog->getUrlKey() . '.html';
        $cmsPageUrl = $_ENV['app_frontend_url'] . $cmsPage->getIdentifier();

        \PHPUnit_Framework_Assert::assertTrue(
            $this->checkContent($content, $productUrl, $catalogUrl, $cmsPageUrl),
            'Content of file sitemap.xml does not including one or more next urls: '
            . "\n" . $productUrl . "\n" . $catalogUrl . "\n" . $cmsPageUrl
        );
    }

    /**
     * Check content for the presence urls
     *
     * @param string $content
     * @param string $productUrl
     * @param string $catalogUrl
     * @param string $cmsPageUrl
     * @return bool
     */
    protected function checkContent($content, $productUrl, $catalogUrl, $cmsPageUrl)
    {
        $productResult = strpos($content, $productUrl);
        $catalogResult = strpos($content, $catalogUrl);
        $cmsPageResult = strpos($content, $cmsPageUrl);
        if ($productResult && $catalogResult && $cmsPageResult) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'File sitemap.xml contains correct content according to dataset.';
    }
}
