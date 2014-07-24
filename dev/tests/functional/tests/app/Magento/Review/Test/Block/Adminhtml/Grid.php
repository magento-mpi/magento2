<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridAbstract;

/**
 * Class Grid
 * Reviews grid
 */
class Grid extends GridAbstract
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'review_id' => [
            'selector' => '#reviwGrid_filter_review_id',
        ],
        'title' => [
            'selector' => '#reviwGrid_filter_title',
        ],
        'status' => [
            'selector' => '#reviwGrid_filter_status',
            'input' => 'select',
        ],
        'nickname' => [
            'selector' => '#reviwGrid_filter_nickname',
        ],
        'detail' => [
            'selector' => '#reviwGrid_filter_detail',
        ],
        'visible_in' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-store-filter-visible-in"]',
            'input' => 'selectstore',
        ],
        'type' => [
            'selector' => '#reviwGrid_filter_type',
            'input' => 'select',
        ],
        'name' => [
            'selector' => '#reviwGrid_filter_name',
        ],
        'sku' => [
            'selector' => '#reviwGrid_filter_sku',
        ],
    ];
}
