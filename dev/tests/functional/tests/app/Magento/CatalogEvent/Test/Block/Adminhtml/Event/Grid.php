<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Event;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class EventGrid
 * Events grid of Catalog Events
 *
 * @package Magento\CatalogEvent\Test\Block\Adminhtml\Event
 */
class Grid extends AbstractGrid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'category_name' => [
            'selector' => 'input[name="category"]'
        ],
        'start_on' => [
            'selector' => '[name="date_start[from]"]'
        ],
        'end_on' => [
            'selector' => '[name="date_end[from]"]'
        ],
        'status' => [
            'selector' => 'select[name="status"]',
            'input' => 'select'
        ],
        'countdown_ticker' => [
            'selector' => 'select[name="display_state"]',
            'input' => 'select'
        ],
        'sort_order' => [
            'selector' => 'input[name="sort_order"]'
        ],
    ];
}
