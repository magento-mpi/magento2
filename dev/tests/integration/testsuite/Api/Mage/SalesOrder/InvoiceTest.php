<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class SalesOrder_InvoiceTest extends SalesOrder_AbstractTest
{
    /**
     * Delete used fixtures
     * Clean up invoice and revert changes to entity store model
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->deleteFixture('invoice', true);
        $this->deleteFixture('invoice2', true);
        $this->deleteFixture('order2', true);
        $this->deleteFixture('quote2', true);
        $this->deleteFixture('order', true);
        $this->deleteFixture('quote', true);
        $this->deleteFixture('product_virtual', true);
        $this->deleteFixture('customer_address', true);
        $this->deleteFixture('customer', true);

        $invoice = Mage::getModel('Mage_Sales_Model_Order_Invoice');
        $invoice->loadByIncrementId(self::getFixture('invoiceIncrementId'));
        $this->callModelDelete($invoice, true);
        $this->_restoreIncrementIdPrefix();

        parent::tearDown();
    }

    /**
     * Test create and read created invoice
     *
     * @magentoDataFixture Api/Mage/SalesOrder/_fixture/order.php
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');
        $id = $order->getIncrementId();

        // Create new invoice
        $newInvoiceId = $this->call(
            'salesOrderInvoiceCreate',
            array(
                'orderIncrementId' => $id,
                'itemsQty' => array(),
                'comment' => 'invoice Created',
                'email' => true,
                'includeComment' => true
            )
        );
        $this->assertNotNull($newInvoiceId);
        self::setFixture('invoiceIncrementId', $newInvoiceId);

        // View new invoice
        $invoice = $this->call(
            'sales_salesOrderInvoiceInfo',
            array(
                'invoiceIncrementId' => $newInvoiceId
            )
        );

        $this->assertEquals($newInvoiceId, $invoice['increment_id']);
    }

    /**
     * Test credit memo create API call results
     *
     * @magentoDataFixture Api/Mage/SalesOrder/_fixture/order.php
     * @magentoAppIsolation enabled
     */
    public function testAutoIncrementType()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $order = self::getFixture('order2');
        $id = $order->getIncrementId();

        // Set invoice increment id prefix
        $prefix = '01';
        $this->_setIncrementIdPrefix('invoice', $prefix);

        // Create new invoice
        $newInvoiceId = $this->call(
            'salesOrderInvoiceCreate',
            array(
                'orderIncrementId' => $id,
                'itemsQty' => array(),
                'comment' => 'invoice Created',
                'email' => true,
                'includeComment' => true
            )
        );

        $this->assertTrue(is_string($newInvoiceId), 'Increment Id is not a string');
        $this->assertStringStartsWith($prefix, $newInvoiceId, 'Increment Id returned by API is not correct');
        self::setFixture('invoiceIncrementId', $newInvoiceId);
    }

    /**
     * Test order invoice list. With filters
     *
     * @magentoDataFixture Api/Mage/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
     */
    public function testListWithFilters()
    {
        /** @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = self::getFixture('invoice');

        $filters = array(
            'filters' => array(
                'filter' => array(
                    array('key' => 'state', 'value' => $invoice->getData('state')),
                    array('key' => 'created_at', 'value' => $invoice->getData('created_at'))
                ),
                'complex_filter' => array(
                    array(
                        'key' => 'invoice_id',
                        'value' => array('key' => 'in', 'value' => array($invoice->getId(), 0))
                    ),
                )
            )
        );

        $result = $this->call('salesOrderInvoiceList', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        $this->assertInternalType('array', $result, "Response has invalid format");
        $this->assertEquals(1, count($result), "Invalid invoices quantity received");
        foreach (reset($result) as $field => $value) {
            if ($field == 'invoice_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($invoice->getData($field), $value, "Field '{$field}' has invalid value");
        }
    }
}
