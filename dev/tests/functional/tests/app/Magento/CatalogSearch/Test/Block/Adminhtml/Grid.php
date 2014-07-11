<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid as WidgetGrid;

/**
 * Class Grid
 * Backend terms grid
 */
class Grid extends WidgetGrid
{
    /**
     * Initialize block elements
     */
    protected $filters = [
        'search_query' => [
            'selector' => '#catalog_search_grid_filter_search_query'
        ],
        'store_id' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-store-filter-store-id"]',
            'input' => 'select'
        ],
        'results_from' => [
            'selector' => '#catalog_search_grid_filter_num_results_from'
        ],
        'popularity_from' => [
            'selector' => '#catalog_search_grid_filter_popularity_from'
        ],
        'synonym_for' => [
            'selector' => '#catalog_search_grid_filter_synonym_for'
        ],
        'redirect' => [
            'selector' => '#catalog_search_grid_filter_redirect'
        ],
        'display_in_terms' => [
            'selector' => '#catalog_search_grid_filter_display_in_terms',
            'input' => 'select'
        ]
    ];
}
