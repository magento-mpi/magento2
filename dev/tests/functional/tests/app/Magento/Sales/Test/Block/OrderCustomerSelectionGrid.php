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

namespace Magento\Sales\Test\Block;

use Magento\Backend\Test\Block\Widget\Grid;

class OrderCustomerSelectionGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'email' => array(
                'selector' => '#customerGrid_filter_email'
            ),
        );
    }

    /**
     * Click create new customer button
     */
    public function createNewCustomer()
    {
        $this->_rootElement->find('.actions button')->click();
        $this->_templateBlock->waitLoader();
    }
}
