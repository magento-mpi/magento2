<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Backend\Test\Block\Widget\Grid;
use Magento\Sales\Test\Fixture\Order;

/**
 * Selection customer grid with option for creating order for the new customer
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class CustomerSelectionGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->editLink = '//td[@data-column="email"]';
        $this->filters = array(
            'email' => array(
                'selector' => '#sales_order_create_customer_grid_filter_email'
            ),
        );
    }

    /**
     * Click create new customer button
     */
    protected function _clickCreateNewCustomer()
    {
        $this->_rootElement->find('.actions button')->click();
    }

    /**
     * Select customer if it is present in fixture or click create new customer button
     *
     * @param Order $fixture
     */
    public function selectCustomer(Order $fixture)
    {
        $customer = $fixture->getCustomer();
        if (empty($customer)) {
            $this->_clickCreateNewCustomer();
        } else {
            $this->searchAndOpen(array(
                'email' => $customer->getEmail()
            ));
        }
        $this->_templateBlock->waitLoader();
    }
}
