<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sitemap\Test\Fixture\Sitemap;
use Magento\Sitemap\Test\Page\Adminhtml\SitemapIndex;

/**
 * Class AssertSitemapInGrid
 *
 * @package Magento\Sitemap\Test\Constraint
 */
class AssertSitemapInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that sitemap availability in sitemap grid
     *
     * @param Sitemap $sitemap
     * @param SitemapIndex $sitemapPageGrid
     * @return void
     */
    public function processAssert(Sitemap $sitemap, SitemapIndex $sitemapPageGrid)
    {
        $sitemapPageGrid->open();
        $filter = [
            'sitemap_filename' => $sitemap->getSitemapFilename(),
            'sitemap_path' => $sitemap->getSitemapPath()
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $sitemapPageGrid->getSitemapGrid()->isRowVisible($filter),
            'Sitemap with filename \'' . $sitemap->getSitemapFilename() . '\' and path \''
            . $sitemap->getSitemapPath() . '\'is absent in Sitemap grid.'
        );
    }

    /**
     * Text of presence sitemap in grid.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sitemap in grid is present.';
    }
}
