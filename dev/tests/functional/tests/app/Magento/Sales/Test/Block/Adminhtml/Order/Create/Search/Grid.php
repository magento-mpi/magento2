<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Search;

/**
 * Class Grid
 * Adminhtml sales order create search products block
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-in_products';

    /**
     * {@inheritdoc}
     */
    protected $filters = [
        'sku' => [
            'selector' => '#sales_order_create_search_grid_filter_sku'
        ]
    ];
}
