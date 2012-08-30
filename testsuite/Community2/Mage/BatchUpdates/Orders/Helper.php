<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License  (OSL 3.0)
 */
class Community2_Mage_BatchUpdates_Orders_Helper extends Mage_Selenium_TestCase
{
    /**
     * Open existing order
     *
     * @param string $searchData
     */
    public function openOrder($searchData)
    {
        $xpathTR = $this->search($searchData, 'sales_order_grid');
        $this->assertNotEquals(null, $xpathTR, 'Orders is not found');
        $orderId = $this->getColumnIdByName('Order #');
        $this->addParameter('order_id', '#' . $this->getText($xpathTR . '//td[' . $orderId . ']'));
        $this->defineIdFromTitle($xpathTR);
        $this->click($xpathTR  . "//a[text()='View']");
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Create order with status "Processing" by creating Invoice
     *
     * @param string $searchData
     */
    public function createProcessingOrderWithInvoice($searchData)
    {
        $this->openOrder($searchData);
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
    }

    /**
     * Create order wit h status "Processing" by creating Shipment
     *
     * @param string $searchData
     */
    public function createProcessingOrderWithShipment($searchData)
    {
        $this->openOrder($searchData);
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * Create order with status "Complete"
     *
     * @param string $searchData
     */
    public function createCompleteOrder($searchData)
    {
        $this->openOrder($searchData);
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * Create order with status "Closed"
     *
     * @param string $searchData
     */
    public function createClosedOrder($searchData)
    {
        $this->openOrder($searchData);
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
    }

    /**
     * Create order with status "Canceled"
     *
     * @param string $searchData
     */
    public function createCanceledOrder($searchData)
    {
        $this->openOrder($searchData);
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel', true);
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * Create order with status "On Hold"
     *
     * @param string $searchData
     */
    public function createHoldedOrder($searchData)
    {
        $this->openOrder($searchData);
        $this->clickButton('hold', true);
        $this->assertMessagePresent('success', 'success_hold_order');
    }
}