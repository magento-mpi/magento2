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
        $urls = [
            $_ENV['app_frontend_url'] . $product->getUrlKey() . '.html',
            $_ENV['app_frontend_url'] . $catalog->getUrlKey() . '.html',
            $_ENV['app_frontend_url'] . $cmsPage->getIdentifier()
        ];

        \PHPUnit_Framework_Assert::assertTrue(
            $this->checkContent($content, $urls),
            'Content of file sitemap.xml does not including one or more next urls: '
            . $this->urlsToString($urls)
        );
    }

    /**
     * Check content for the presence urls
     *
     * @param string $content
     * @param array $urls
     * @return bool
     */
    protected function checkContent($content, $urls)
    {
        $result = [];
        foreach ($urls as $url) {
            if (strpos($content, $url)) {
                $result[] = strpos($content, $url);
            }
        }
        if (count($result) == count($urls)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Convert urls array to string
     *
     * @param array $urls
     * @return string
     */
    protected function urlsToString($urls)
    {
        $urlsStr = '';
        foreach ($urls as $url) {
            $urlsStr .= "\n" . $url;
        }

        return $urlsStr;
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
