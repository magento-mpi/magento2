<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sitemap\Test\Constraint;

use Magento\Sitemap\Test\Fixture\Sitemap;
use Magento\Sitemap\Test\Page\Adminhtml\SitemapIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertSitemapInGrid
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
     * @param SitemapIndex $sitemapIndex
     * @return void
     */
    public function processAssert(Sitemap $sitemap, SitemapIndex $sitemapIndex)
    {
        $sitemapIndex->open()->getSitemapGrid()->sortGridByField('sitemap_id');
        $filter = [
            'sitemap_filename' => $sitemap->getSitemapFilename(),
            'sitemap_path' => $sitemap->getSitemapPath(),
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $sitemapIndex->getSitemapGrid()->isRowVisible($filter),
            'Sitemap with filename \'' . $sitemap->getSitemapFilename() . '\' and path \''
            . $sitemap->getSitemapPath() . '\' is absent in Sitemap grid. \''
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
