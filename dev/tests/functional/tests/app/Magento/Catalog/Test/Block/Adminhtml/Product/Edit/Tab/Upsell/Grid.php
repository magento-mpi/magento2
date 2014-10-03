<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Related up sell products grid
 */
class Grid extends GridInterface
{
    /**
     * Grid fields map
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name="name"]'
        ],
        'sku' => [
            'selector' => 'input[name="sku"]'
        ],
        'type' => [
            'selector' => 'select[name="type"]',
            'input' => 'select'
        ]
    ];
}
