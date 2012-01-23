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
 * @magentoDataFixture Api/SalesOrder/_fixtures/shipment.php
 */
class Api_SalesOrder_ShipmentTest extends Magento_Test_Webservice
{
    /**
     * Clean up shipment and revert changes to entity store model
     *
     * @return void
     */
    protected function tearDown()
    {
        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->loadByIncrementId($this->getFixture('shipmentIncrementId'));
        $this->callModelDelete($shipment, true);

        $entityStoreModel = $this->getFixture('entity_store_model');
        if ($entityStoreModel instanceof Mage_Eav_Model_Entity_Store) {
            $origIncrementData = $this->getFixture('orig_shipping_increment_data');
            $entityStoreModel->loadByEntityStore($entityStoreModel->getEntityTypeId(),$entityStoreModel->getStoreId());
            $entityStoreModel->setIncrementPrefix($origIncrementData['prefix'])
                ->setIncrementLastId($origIncrementData['increment_last_id'])
                ->save();
        }

        parent::tearDown();
    }

    public function testCRUD()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');

        $id = $order->getIncrementId();

        // Create new shipment
        $newShipmentId = $this->call('order_shipment.create', array(
            $id,
            array(),
            'Shipment Created',
            true,
            true
        ));

        // View new shipment
        $shipment = $this->call('sales_order_shipment.info', $newShipmentId);

        //var_dump($shipment);
    }

    /**
     * Test shipment create API call results
     *
     * @return void
     */
    public function testAutoIncrementType()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $this->getFixture('quote');
        //Create order
        $quoteService = new Mage_Sales_Model_Service_Quote($quote);
        //Set payment method to check/money order
        $quoteService->getQuote()->getPayment()->setMethod('checkmo');
        $order = $quoteService->submitOrder();
        $order->place();
        $order->save();
        $id = $order->getIncrementId();

        // Set shipping increment id prefix
        $website = Mage::app()->getWebsite();
        $entityTypeModel = Mage::getModel('eav/entity_type')->loadByCode('shipment');
        $entityStoreModel = Mage::getModel('eav/entity_store')->loadByEntityStore($entityTypeModel->getId(),$website->getDefaultStore()->getId());
        Magento_Test_Webservice::setFixture('orig_shipping_increment_data', array(
            'prefix' => $entityStoreModel->getIncrementPrefix(),
            'increment_last_id' => $entityStoreModel->getIncrementLastId()
        ));
        $entityStoreModel->setIncrementPrefix('01');
        $entityStoreModel->save();
        Magento_Test_Webservice::setFixture('entity_store_model', $entityStoreModel);

        // Create new shipment
        $newShipmentId = $this->call('order_shipment.create', array(
            'orderIncrementId' => $id,
            'itemsQty' => array(),
            'comment' => 'Shipment Created',
            'email' => true,
            'includeComment' => true
        ));
        $this->setFixture('shipmentIncrementId', $newShipmentId);

        $this->assertTrue(is_string($newShipmentId), 'Increment Id is not a string');
        $entityStoreModel = $this->getFixture('entity_store_model');
        $this->assertStringStartsWith($entityStoreModel->getIncrementPrefix(), $newShipmentId,
            'Increment Id returned by API is not correct');
    }
}
