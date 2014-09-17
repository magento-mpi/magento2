<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * Adminhtml Gift Wrapping management grid
 */
class Grid extends ParentGrid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'wrapping_id_from' => [
            'selector' => 'input[name="wrapping_id[from]"]',
        ],
        'wrapping_id_to' => [
            'selector' => 'input[name="wrapping_id[to]"]',
        ],
        'design' => [
            'selector' => '#giftwrappingGrid_filter_design',
        ],
        'status' => [
            'selector' => '#giftwrappingGrid_filter_status',
            'input' => 'select',
        ],
        'website_ids' => [
            'selector' => '#giftwrappingGrid_filter_websites',
            'input' => 'select',
        ],
        'base_price' => [
            'selector' => '#giftwrappingGrid_filter_base_price_from',
        ],
    ];
}
