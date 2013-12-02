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

namespace Magento\Backend\Test\Block\Sales\Order;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Sales order grid
 *
 * @package Magento\Backend\Test\Block\Sales\Order
 */
class Grid extends GridInterface
{
    /**
     * Purchase Point Filter selector
     *
     * @var string
     */
    protected $purchasePointFilter = '//*[@data-ui-id="widget-grid-column-filter-store-filter-store-id"]';

    /**
     * Purchase Point Filter option group ellements selector
     *
     * @var string
     */
    protected $purchasePointOptionGroup = '//*[@data-ui-id="widget-grid-column-filter-store-filter-store-id"]/optgroup';

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'id' => array(
                'selector' => '#sales_order_grid_filter_real_order_id'
            ),
            'status' => array(
                'selector' => '#sales_order_grid_filter_status',
                'input' => 'select'
            )
        );
    }

    /**
     * Get selected data from Purchase Point filter
     *
     * @return string
     */
    public function getPurchasePointFilterText()
    {
        return $this->_rootElement->find($this->purchasePointFilter, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Purchase Point Filter option group ellements
     *
     * @return mixed|\Mtf\Client\Element
     */
    public function getPurchasePointFilterOptionsGroup()
    {
        return $this->_rootElement->find($this->purchasePointOptionGroup, Locator::SELECTOR_XPATH);
    }
}
