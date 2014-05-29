<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Block\Adminhtml\Promo;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class PromoQuoteGrid
 * Backend sales rule grid
 */
class Grid extends AbstractGrid
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
     * Locator value for link in sales rule name column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-name]';
}
