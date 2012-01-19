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
     * Test related to APIA-160 task
     */
    public function testAutoIncrementType()
    {
        //TODO force order_shipment.create return string instead of integer,
        //e.g. '0123' should not be cropped to '123'
    }
}
