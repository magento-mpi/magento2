<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Block;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Adminhtml Cms Block management grid
 */
class Grid extends GridInterface
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => '#cmsBlockGrid_filter_title'
        ],
        'identifier' => [
            'selector' => '#cmsBlockGrid_filter_identifier',
        ],
        'store_id' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-store-filter-store-id"]',
            'input' => 'select',
        ],
        'is_active' => [
            'selector' => '#cmsBlockGrid_filter_is_active',
            'input' => 'select',
        ],
        'creation_time' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-datetime-filter-creation-time-from"]',
        ],
        'update_time' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-datetime-1-filter-update-time-from"]',
        ],
    ];
}
