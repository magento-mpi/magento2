<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * 'Add Products to Bundle Option' grid
 */
class Grid extends GridInterface
{
    /**
     * Selector for 'Add Selected Products' button
     *
     * @var string
     */
    protected $addProducts = 'button.add';

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-id';

    /**
     * Filters param for grid
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name=name]'
        ],
        'sku' => [
            'selector' => 'input[name=sku]'
        ],
    ];

    /**
     * Press 'Add Selected Products' button
     *
     * @return void
     */
    public function addProducts()
    {
        $this->_rootElement->find($this->addProducts)->click();
    }
}
