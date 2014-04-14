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
class ProductGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected $filters = array(
        'name' => array(
            'selector' => '#productGrid_product_filter_name'
        ),
        'sku' => array(
            'selector' => '#productGrid_product_filter_sku'
        ),
        'type' => array(
            'selector' => '#productGrid_product_filter_type',
            'input' => 'select'
        ),
        'price_from' => array(
            'selector' => '#productGrid_product_filter_price_from'
        ),
        'price_to' => array(
            'selector' => '#productGrid_product_filter_price_to'
        )
    );

    /**
     * Update attributes for selected items
     *
     * @param array $items
     */
    public function updateAttributes(array $items = array())
    {
        $this->massaction('Update Attributes', $items);
    }
}
