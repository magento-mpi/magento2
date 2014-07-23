<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml\Product;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * Review catalog product grid
 */
class Grid extends ParentGrid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'id' => [
            'selector' => 'input[name="entity_id"]'
        ],
        'name' => [
            'selector' => 'input[name="name"]'
        ]
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = '.col-entity_id';
}
