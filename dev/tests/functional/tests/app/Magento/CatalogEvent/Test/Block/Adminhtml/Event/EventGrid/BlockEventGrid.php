<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Event\EventGrid;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class EventGrid
 * Events' grid of Catalog Products
 *
 * @package Magento\CatalogEvent\Test\Block\Adminhtml\Event\EventGrid
 */
class BlockEventGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'category_name' => [
            'selector' => '#catalogEventGrid_filter_category'
        ],
        'start_on' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-datetime-filter-date-start-from"]'
        ],
        'end_on' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-datetime-1-filter-date-end-from"]'
        ],
        'status' => [
            'selector' => '#catalogEventGrid_filter_status',
            'input' => 'select'
        ],
        'countdown_ticker' => [
            'selector' => '#catalogEventGrid_filter_display_state',
            'input' => 'select'
        ],
        'sort_order' => [
            'selector' => '#catalogEventGrid_filter_sort_order'
        ],
    ];

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .even pointer';
}