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
 * Class CmsGrid
 * Adminhtml Cms Block management grid
 */
class CmsGrid extends GridInterface
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
        'creation_time_from' => [
            'selector' => '[name="creation_time[from]"]',
        ],
        'update_time_from' => [
            'selector' => '[name="update_time[from]"]',
        ],
    ];
}
