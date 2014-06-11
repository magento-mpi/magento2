<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Block\Product\Grouped\AssociatedProducts\Search;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * 'Add Products to Grouped product list' grid
 */
class Grid extends GridInterface
{
    /**
     * 'Add Selected Products' button
     *
     * @var string
     */
    protected $addProducts = 'button.add';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'sku' => [
            'selector' => '#grouped_grid_popup_filter_sku'
        ]
    ];

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = '[data-column=entity_id] input';

    /**
     * Press 'Add Selected Products' button
     */
    public function addProducts()
    {
        $this->_rootElement->find($this->addProducts)->click();
    }
}
