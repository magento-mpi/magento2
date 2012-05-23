<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Api_SalesOrder_InvoiceTest extends Magento_Test_Webservice
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

        $invoice = new Mage_Sales_Model_Order_Invoice();
        $invoice->loadByIncrementId(self::getFixture('invoiceIncrementId'));
        $this->callModelDelete($invoice, true);
        $entityStoreModel = self::getFixture('entity_store_model');
        if ($entityStoreModel instanceof Mage_Eav_Model_Entity_Store) {
            $origIncrementData = self::getFixture('orig_invoice_increment_data');
            $entityStoreModel->loadByEntityStore($entityStoreModel->getEntityTypeId(), $entityStoreModel->getStoreId());
            $entityStoreModel->setIncrementLastId($origIncrementData['prefix']
                . substr($entityStoreModel->getIncrementLastId(), -8));
            $entityStoreModel->setIncrementPrefix($origIncrementData['prefix']);
            $entityStoreModel->save();
        }

        parent::tearDown();
    }

    /**
     * Test create and reaad created invoice
     * @magentoDataFixture Api/SalesOrder/_fixtures/order.php
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');
        $id = $order->getIncrementId();

        // Create new invoice
        $newInvoiceId = $this->call('order_invoice.create', array(
            'orderIncrementId' => $id,
            'itemsQty' => array(),
            'comment' => 'invoice Created',
            'email' => true,
            'includeComment' => true
        ));
        $this->assertNotNull($newInvoiceId);
        self::setFixture('invoiceIncrementId', $newInvoiceId);

        // View new invoice
        $invoice = $this->call('sales_order_invoice.info', array(
            'invoiceIncrementId' => $newInvoiceId
        ));

        $this->assertEquals($newInvoiceId, $invoice['increment_id']);
    }

    /**
     * Test credit memo create API call results
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/order.php
     * @magentoAppIsolation enabled
     */
    public function testAutoIncrementType()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $order = self::getFixture('order2');
        $id = $order->getIncrementId();

        // Set invoice increment id prefix
        $website = Mage::app()->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('invoice');
        $entityStoreModel = Mage::getModel('Mage_Eav_Model_Entity_Store')
            ->loadByEntityStore($entityTypeModel->getId(), $storeId);
        $prefix = $entityStoreModel->getIncrementPrefix() == null ? $storeId : $entityStoreModel->getIncrementPrefix();
        self::setFixture('orig_invoice_increment_data', array(
            'prefix' => $prefix,
            'increment_last_id' => $entityStoreModel->getIncrementLastId()
        ));
        $entityStoreModel->setEntityTypeId($entityTypeModel->getId());
        $entityStoreModel->setStoreId($storeId);
        $entityStoreModel->setIncrementPrefix('01');
        $entityStoreModel->save();
        self::setFixture('entity_store_model', $entityStoreModel);

        // Create new invoice
        $newInvoiceId = $this->call('order_invoice.create', array(
            'orderIncrementId' => $id,
            'itemsQty' => array(),
            'comment' => 'invoice Created',
            'email' => true,
            'includeComment' => true
        ));

        $this->assertTrue(is_string($newInvoiceId), 'Increment Id is not a string');
        $this->assertStringStartsWith($entityStoreModel->getIncrementPrefix(), $newInvoiceId,
            'Increment Id returned by API is not correct');
        self::setFixture('invoiceIncrementId', $newInvoiceId);
    }

    /**
     * Test order invoice list. With filters
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/invoice.php
     * @magentoAppIsolation enabled
     */
    public function testListWithFilters()
    {
        /** @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = self::getFixture('invoice');

        if (self::_isSoapV2()) {
            $filters = array('filters' => array(
                'filter' => array(
                    array('key' => 'state', 'value' => $invoice->getData('state')),
                    array('key' => 'created_at', 'value' => $invoice->getData('created_at'))
                ),
                'complex_filter' => array(
                    array(
                        'key'   => 'invoice_id',
                        'value' => array('key' => 'in', 'value' => array($invoice->getId(), 0))
                    ),
                )
            ));
        } else {
            $filters = array(array(
                'state' => array('0', $invoice->getData('state')),
                'created_at' => $invoice->getData('created_at'),
                'invoice_id' => array('in' => array($invoice->getId(), 0))
            ));
        }
        $result = $this->call('order_invoice.list', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        $this->assertInternalType('array', $result, "Response has invalid format");
        $this->assertEquals(1, count($result), "Invalid invoices quantity received");
        foreach(reset($result) as $field => $value) {
            if ($field == 'invoice_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($invoice->getData($field), $value, "Field '{$field}' has invalid value");
        }
    }

    /**
     * Check if SOAP API is testsd
     *
     * @return bool
     */
    protected static function _isSoapV2()
    {
        return TESTS_WEBSERVICE_TYPE == self::TYPE_SOAPV2 || TESTS_WEBSERVICE_TYPE == self::TYPE_SOAPV2_WSI;
    }
}
