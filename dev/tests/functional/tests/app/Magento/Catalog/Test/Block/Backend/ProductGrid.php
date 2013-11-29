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
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = "//a[normalize-space(text())='Edit']";

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'name' => array(
                'selector' => '#productGrid_product_filter_name'
            ),
            'sku' => array(
                'selector' => '#productGrid_product_filter_sku'
            ),
            'type' => array(
                'selector' => '#productGrid_product_filter_type',
                'input' => 'select'
            )
        );
    }
}
