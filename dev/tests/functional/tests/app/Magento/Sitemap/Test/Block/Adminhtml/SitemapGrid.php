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
 */
class SitemapGrid extends Grid
{
    protected $filters = array(
        'sitemap_filename' => array(
            'selector' => '#sitemapGrid_filter_sitemap_filename',
        ),
        'sitemap_path' => array(
            'selector' => '#sitemapGrid_filter_sitemap_path'
        )
    );
}