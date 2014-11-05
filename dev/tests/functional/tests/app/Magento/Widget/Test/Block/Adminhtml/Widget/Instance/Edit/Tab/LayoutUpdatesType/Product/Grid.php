<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\Product;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Chooser product grid
 */
class Grid extends GridInterface
{
    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr td.a-center.col-in_products';

    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name="chooser_name"]'
        ],
        'sku' => [
            'selector' => 'input[name="chooser_sku"]'
        ],
    ];
}
