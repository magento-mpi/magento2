<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Backend;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class ProductGrid
 * Backend catalog product grid
 *
 * @package Magento\Catalog\Test\Block
 */
class ProductUpsellGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'name' => array(
                'selector' => '#up_sell_product_grid_filter_name'
            ),
            'sku' => array(
                'selector' => '#up_sell_product_grid_filter_sku'
            ),
            'type' => array(
                'selector' => '#up_sell_product_grid_filter_type',
                'input' => 'select'
            )
        );
    }
}
