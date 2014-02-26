<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\Grouped\AssociatedProducts\Search;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 *
 * @package Magento\Catalog\Test\Block\Product\Grouped\AssociatedProducts\Search
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
    protected $filters = array(
        'name' => array(
            'selector' => '#grouped_grid_popup_filter_name'
        ),
        'sku' => array(
            'selector' => '#grouped_grid_popup_filter_sku'
        ),
    );

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->selectItem = "[data-column=entity_id] input";
    }

    /**
     * Press 'Add Selected Products' button
     */
    public function addProducts()
    {
        $this->_rootElement->find($this->addProducts)->click();
    }
}
