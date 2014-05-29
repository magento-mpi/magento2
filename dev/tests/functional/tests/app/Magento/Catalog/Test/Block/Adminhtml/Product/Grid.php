<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * Backend catalog product grid
 */
class Grid extends ParentGrid
{
    /**
     * Initialize block elements
     */
    protected $filters = [
        'name' => [
            'selector' => '#productGrid_product_filter_name'
        ],
        'sku' => [
            'selector' => '#productGrid_product_filter_sku'
        ],
        'type' => [
            'selector' => '#productGrid_product_filter_type',
            'input' => 'select'
        ],
        'price_from' => [
            'selector' => '#productGrid_product_filter_price_from'
        ],
        'price_to' => [
            'selector' => '#productGrid_product_filter_price_to'
        ]
    ];

    /**
     * Update attributes for selected items
     *
     * @param array $items
     * @return void
     */
    public function updateAttributes(array $items = array())
    {
        $this->massaction('Update Attributes', $items);
    }
}
 
