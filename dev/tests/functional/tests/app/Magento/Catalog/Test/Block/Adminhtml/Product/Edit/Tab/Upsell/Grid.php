<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

class Grid extends GridInterface
{
    protected $filters = [
        'name' => [
            'selector' => '#up_sell_product_grid_filter_name'
        ],
        'sku' => [
            'selector' => '#up_sell_product_grid_filter_sku'
        ],
        'type' => [
            'selector' => '#up_sell_product_grid_filter_type',
            'input' => 'select'
        ]
    ];
}
