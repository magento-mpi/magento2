<?php
/**
 * Tests for invoice API.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Sales_Model_Order_Invoice_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test create and read created invoice
     *
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCreate()
    {
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_FRONTEND)
            ->setDefaultDesignTheme();
        /** Prepare data. */
        $order = $this->_getFixtureOrder();
        $this->assertCount(
            0,
            $order->getInvoiceCollection(),
            'There must be 0 invoices before invoice creation via API.'
        );

        /** Create new invoice via API. */
        $newInvoiceId = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInvoiceCreate',
            array(
                'orderIncrementId' => $order->getIncrementId(),
                'itemsQty' => array(),
                'comment' => 'invoice Created',
                'email' => true,
                'includeComment' => true
            )
        );
        $this->assertGreaterThan(0, (int)$newInvoiceId, 'Invoice was not created.');

        /** Ensure that invoice was created. */
        /** @var Magento_Sales_Model_Order $invoicedOrder */
        $invoicedOrder = Mage::getModel('Magento_Sales_Model_Order');
        $invoicedOrder->loadByIncrementId($order->getIncrementId());
        $invoiceCollection = $invoicedOrder->getInvoiceCollection();
        $this->assertCount(1, $invoiceCollection->getItems(), 'Invoice was not created.');
        /** @var Magento_Sales_Model_Order_Invoice $createdInvoice */
        $createdInvoice = $invoiceCollection->getFirstItem();
        $this->assertEquals(
            $createdInvoice->getIncrementId(),
            $newInvoiceId,
            'Invoice ID in call response is invalid.'
        );
    }

    /**
     * Test create and read created invoice
     *
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInfo()
    {
        /** Retrieve invoice data via API. */
        $invoiceData = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInvoiceInfo',
            array(
                $this->_getFixtureInvoice()->getIncrementId(),
            )
        );

        /** Check received data validity. */
        $fieldsToCheck = array(
            'increment_id',
            'store_id',
            'order_id',
            'state',
            'entity_id' => 'invoice_id',
            'base_grand_total'
        );
        Magento_Test_Helper_Api::checkEntityFields(
            $this,
            $this->_getFixtureInvoice()->getData(),
            $invoiceData,
            $fieldsToCheck
        );
    }

    /**
     * Test adding comment to invoice via API.
     *
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     * @magentoDbIsolation enabled
     */
    public function testAddComment()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        /** Prepare data. */
        $commentText = "Test invoice comment.";

        /** Retrieve invoice data via API. */
        $isAdded = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInvoiceAddComment',
            array(
                $this->_getFixtureInvoice()->getIncrementId(),
                $commentText,
                true, // send invoice via email
                true // include comment in email
            )
        );
        $this->assertTrue($isAdded, "Comment was not added to the invoice.");

        /** Verify that comment was actually added. */
        /** @var Magento_Sales_Model_Resource_Order_Invoice_Comment_Collection $commentsCollection */
        $commentsCollection = $this->_getFixtureInvoice()->getCommentsCollection(true);
        $this->assertCount(1, $commentsCollection->getItems(), "There must be exactly 1 invoice comment.");
        /** @var Magento_Sales_Model_Order_Invoice_Comment $createdComment */
        $createdComment = $commentsCollection->getFirstItem();
        $this->assertEquals($commentText, $createdComment->getComment(), 'Invoice comment text is invalid.');
    }

    /**
     * Test capturing invoice via API.
     *
     * @magentoDataFixture Magento/Sales/_files/invoice_verisign.php
     */
    public function testCapture()
    {
        /** Capture invoice data via API. */
        $invoiceBefore = $this->_getFixtureInvoice();
        $this->assertTrue($invoiceBefore->canCapture(), "Invoice fixture cannot be captured.");
        try {
            $invoiceBefore->capture();
        } catch (Exception $e) {
            $expectedFaultMessage = $e->getMessage();
            /**
             * To avoid complicated environment emulation for online payment,
             * we can check if proper error message from payment gateway was received or not.
             */
            $this->setExpectedException('SoapFault', $expectedFaultMessage);
        }
        Magento_Test_Helper_Api::call($this, 'salesOrderInvoiceCapture', array($invoiceBefore->getIncrementId()));
    }

    /**
     * Test voiding captured invoice via API.
     *
     * @magentoDataFixture Magento/Sales/_files/invoice_verisign.php
     */
    public function testVoid()
    {
        /** Prepare data. Make invoice voidable. */
        $invoiceBefore = $this->_getFixtureInvoice();
        $invoiceBefore->setState(Magento_Sales_Model_Order_Invoice::STATE_PAID)->setCanVoidFlag(true)->save();
        /** Check if invoice can be voided via API. */
        $this->assertTrue($invoiceBefore->canVoid(), "Invoice fixture cannot be voided.");

        try {
            $invoiceBefore->void();
        } catch (Exception $e) {
            $expectedFaultMessage = $e->getMessage();
            /**
             * To avoid complicated environment emulation for online voiding,
             * we can check if proper error message from payment gateway was received or not.
             */
            $this->setExpectedException('SoapFault', $expectedFaultMessage);
        }

        Magento_Test_Helper_Api::call($this, 'salesOrderInvoiceVoid', array($invoiceBefore->getIncrementId()));
    }

    /**
     * Test cancelling invoice via API.
     *
     * @magentoDataFixture Magento/Sales/_files/invoice_verisign.php
     */
    public function testCancel()
    {
        /** Capture invoice data via API. */
        $invoiceBefore = $this->_getFixtureInvoice();
        $this->assertTrue($invoiceBefore->canCancel(), "Invoice fixture cannot be cancelled.");
        $isCanceled = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInvoiceCancel',
            array($invoiceBefore->getIncrementId())
        );
        $this->assertTrue($isCanceled, "Invoice was not canceled successfully.");

        /** Ensure that invoice was actually cancelled. */
        $invoiceAfter = $this->_getFixtureInvoice();
        $this->assertEquals(
            Magento_Sales_Model_Order_Invoice::STATE_CANCELED,
            $invoiceAfter->getState(),
            "Invoice was not cancelled."
        );
    }

    /**
     * Retrieve invoice declared in fixture.
     *
     * This method reloads data and creates new object with each call.
     *
     * @return Magento_Sales_Model_Order_Invoice
     */
    protected function _getFixtureInvoice()
    {
        $order = $this->_getFixtureOrder();
        $invoiceCollection = $order->getInvoiceCollection();
        $this->assertCount(1, $invoiceCollection->getItems(), 'There must be exactly 1 invoice assigned to the order.');
        /** @var Magento_Sales_Model_Order_Invoice $invoice */
        $invoice = $invoiceCollection->getFirstItem();
        return $invoice;
    }

    /**
     * Retrieve order declared in fixture.
     *
     * This method reloads data and creates new object with each call.
     *
     * @return Magento_Sales_Model_Order
     */
    protected function _getFixtureOrder()
    {
        $orderIncrementId = '100000001';
        /** @var Magento_Sales_Model_Order $order */
        $order = Mage::getModel('Magento_Sales_Model_Order');
        $order->loadByIncrementId($orderIncrementId);
        return $order;
    }

    /**
     * Test credit memo create API call results
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/order.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testAutoIncrementType()
    {
        /** @var $quote Magento_Sales_Model_Quote */
        $order = Mage::registry('order2');
        $incrementId = $order->getIncrementId();

        // Set invoice increment id prefix
        $prefix = '01';
        Magento_Test_Helper_Eav::setIncrementIdPrefix('invoice', $prefix);

        // Create new invoice
        $newInvoiceId = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInvoiceCreate',
            array(
                'orderIncrementId' => $incrementId,
                'itemsQty' => array(),
                'comment' => 'invoice Created',
                'email' => true,
                'includeComment' => true
            )
        );

        $this->assertTrue(is_string($newInvoiceId), 'Increment Id is not a string');
        $this->assertStringStartsWith($prefix, $newInvoiceId, 'Increment Id returned by API is not correct');
        Mage::register('invoiceIncrementId', $newInvoiceId);
    }

    /**
     * Test order invoice list. With filters
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/multiple_invoices.php
     * @magentoAppIsolation enabled
     */
    public function testListWithFilters()
    {
        /** @var $invoice Magento_Sales_Model_Order_Invoice */
        $invoice = Mage::registry('invoice');

        $filters = array(
            'filters' => (object)array(
                'filter' => array(
                    (object)array('key' => 'state', 'value' => $invoice->getData('state')),
                    (object)array('key' => 'created_at', 'value' => $invoice->getData('created_at'))
                ),
                'complex_filter' => array(
                    (object)array(
                        'key' => 'invoice_id',
                        'value' => (object)array('key' => 'in', 'value' => array($invoice->getId(), 0))
                    ),
                )
            )
        );

        $result = Magento_Test_Helper_Api::call($this, 'salesOrderInvoiceList', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        $this->assertInternalType('array', $result, "Response has invalid format");
        $this->assertEquals(1, count($result), "Invalid invoices quantity received");

        /** Reload invoice data to ensure it is up to date. */
        $invoice->load($invoice->getId());
        foreach (reset($result) as $field => $value) {
            if ($field == 'invoice_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($invoice->getData($field), $value, "Field '{$field}' has invalid value");
        }
    }
}
