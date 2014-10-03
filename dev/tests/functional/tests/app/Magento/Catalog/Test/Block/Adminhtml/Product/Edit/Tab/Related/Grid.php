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
