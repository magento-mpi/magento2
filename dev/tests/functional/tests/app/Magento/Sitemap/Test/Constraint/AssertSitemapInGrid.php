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
use Magento\Sitemap\Test\Page\Adminhtml\AdminSitemapIndex;

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
     * Assert product availability in Products Grid
     *
     * @param Sitemap $sitemap
     * @param AdminSitemapIndex $sitemapPageGrid
     * @return void
     */
    public function processAssert(Sitemap $sitemap, AdminSitemapIndex $sitemapPageGrid)
    {
        $filter = ['sitemap_filename' => $sitemap->getSitemapFilename(), 'sitemap_path' => $sitemap->getSitemapPath()];
        \PHPUnit_Framework_Assert::assertTrue(
            $sitemapPageGrid->getSitemapGrid()->isRowVisible($filter),
            'Sitemap with filename \'' . $sitemap->getSitemapFilename() . '\' and path \'' . $sitemap->getSitemapPath(
            ) . '\'is absent in Sitemap grid.'
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
