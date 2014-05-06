<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Event;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class EventGrid
 * Events' grid of Catalog Events
 *
 * @package Magento\CatalogEvent\Test\Block\Adminhtml\Event
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
            'selector' => '[name="category"]'
        ],
        'start_on' => [
            'selector' => '[name="date_start[from]"]'
        ],
        'end_on' => [
            'selector' => '[name="date_end[from]"]'
        ],
        'status' => [
            'selector' => '[name="status"]',
            'input' => 'select'
        ],
        'countdown_ticker' => [
            'selector' => '[name="display_state"]',
            'input' => 'select'
        ],
        'sort_order' => [
            'selector' => '[name="sort_order"]'
        ],
    ];

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .even pointer';
}
