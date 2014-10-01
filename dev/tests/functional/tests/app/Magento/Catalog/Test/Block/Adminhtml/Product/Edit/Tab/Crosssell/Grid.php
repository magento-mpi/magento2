<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

class Grid extends GridInterface
{
    /**
     * Grid fields map
     *
     * @var array
     */
    protected $filters = array(
        'name' => array(
            'selector' => '#cross_sell_product_grid_filter_name'
        ),
        'sku' => array(
            'selector' => '#cross_sell_product_grid_filter_sku'
        ),
        'type' => array(
            'selector' => '#cross_sell_product_grid_filter_type',
            'input' => 'select'
        )
    );
}
