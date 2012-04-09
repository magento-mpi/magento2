<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoDataFixture Api/SalesOrder/_fixtures/order.php
 */
class Api_SalesOrder_InvoiceTest extends Magento_Test_Webservice
{
    /**
     * Clean up invoice and revert changes to entity store model
     *
     * @return void
     */
    protected function tearDown()
    {
        $invoice = new Mage_Sales_Model_Order_Invoice();
        $invoice->loadByIncrementId(self::getFixture('invoiceIncrementId'));
        $this->callModelDelete($invoice, true);

        $entityStoreModel = self::getFixture('entity_store_model');
        if ($entityStoreModel instanceof Mage_Eav_Model_Entity_Store) {
            $origIncrementData = self::getFixture('orig_invoice_increment_data');
            $entityStoreModel->loadByEntityStore($entityStoreModel->getEntityTypeId(), $entityStoreModel->getStoreId());
            $entityStoreModel->setIncrementPrefix($origIncrementData['prefix'])
                ->save();
        }

        parent::tearDown();
    }

    /**
     * Test credit memo create API call results
     *
     * @return void
     */
    public function testAutoIncrementType()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $order = self::getFixture('order');
        $id = $order->getIncrementId();

        // Set invoice increment id prefix
        $website = Mage::app()->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Mage::getModel('eav/entity_type')->loadByCode('invoice');
        $entityStoreModel = Mage::getModel('eav/entity_store')
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
            'invoiceIncrementId' => $id,
            'itemsQty' => array(),
            'comment' => 'invoice Created',
            'email' => true,
            'includeComment' => true
        ));
        self::setFixture('invoiceIncrementId', $newInvoiceId);

        $this->assertTrue(is_string($newInvoiceId), 'Increment Id is not a string');
        $this->assertStringStartsWith($entityStoreModel->getIncrementPrefix(), $newInvoiceId,
            'Increment Id returned by API is not correct');
    }
}
