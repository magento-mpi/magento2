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

namespace Magento\Sales\Test\Block\Backend\Order;


Class PageActions extends \Magento\Backend\Test\Block\PageActions
{
    /**
     * Click "Ship" button
     */
    public function clickShipButton()
    {
        $this->_rootElement->find('#order_ship')->click();
    }

    /**
     * Click "Invoice" button
     */
    public function clickInvoiceButton()
    {
        $this->_rootElement->find('#order_invoice')->click();
    }
}