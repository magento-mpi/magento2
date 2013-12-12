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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

class Grid extends GridInterface
{
    protected $filters = array(
        'name' => array(
            'selector' => '#up_sell_product_grid_filter_name'
        ),
        'sku' => array(
            'selector' => '#up_sell_product_grid_filter_sku'
        ),
        'type' => array(
            'selector' => '#up_sell_product_grid_filter_type',
            'input' => 'select'
        )
    );

    /**
     * @param array $filter
     */
    public function searchAndSelect(array $filter)
    {
        $element = $this->_rootElement;
        $resetButton = $this->resetButton;
        $this->_rootElement->waitUntil(
            function () use ($element, $resetButton) {
                return $element->find($resetButton)->isVisible() ? true : null;
            }
        );
        parent::searchAndSelect($filter);
    }
}
