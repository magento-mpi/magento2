<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Adminhtml Gift Wrapping management grid
 */
class Grid extends GridInterface
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
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
    ];
}
