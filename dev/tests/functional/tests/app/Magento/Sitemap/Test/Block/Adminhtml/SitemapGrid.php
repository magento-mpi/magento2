<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class SitemapGrid
 * Backend sitemap grid
 *
 * @package Magento\Sitemap\Test\Block\Adminhtml
 */
class SitemapGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'sitemap_filename' => [
            'selector' => '#sitemapGrid_filter_sitemap_filename',
        ],
        'sitemap_path' => [
            'selector' => '#sitemapGrid_filter_sitemap_path'
        ],
        'sitemap_id' => [
            'selector' => '#sitemapGrid_filter_sitemap_id'
        ]
    ];

    /**
     * Locator value for link in sitemap id column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-sitemap_id]';
}
