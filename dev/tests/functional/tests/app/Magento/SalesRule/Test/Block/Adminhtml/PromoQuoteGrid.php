<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class PromoQuoteGrid
 * Backend sales rule grid
 */
class PromoQuoteGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '#promo_quote_grid_filter_name',
        ]
    ];

    /**
     * Locator value for link in sitemap name column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-name]';
}
