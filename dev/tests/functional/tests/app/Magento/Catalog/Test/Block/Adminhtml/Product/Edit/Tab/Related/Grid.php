<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

class Grid extends GridInterface
{
    protected $filters = [
        'name' => [
            'selector' => '#related_product_grid_filter_name'
        ],
        'sku' => [
            'selector' => '#related_product_grid_filter_sku'
        ],
        'type' => [
            'selector' => '#related_product_grid_filter_type',
            'input' => 'select'
        ]
    ];
}
