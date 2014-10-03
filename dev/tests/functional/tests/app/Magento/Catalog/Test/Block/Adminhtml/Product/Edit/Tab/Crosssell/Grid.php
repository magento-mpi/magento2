<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Cross sell products grid
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
            'selector' => '[name="name"]'
        ],
        'sku' => [
            'selector' => '[name="sku"]'
        ],
        'type' => [
            'selector' => '[name="type"]',
            'input' => 'select'
        ]
    ];
}
