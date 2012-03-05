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
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for order address (customer) API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Sales_Order_Address_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_Test_Webservice::deleteFixture('customer_order', true);
        Magento_Test_Webservice::deleteFixture('customer_quote', true);
        Magento_Test_Webservice::deleteFixture('customer_product1', true);
        Magento_Test_Webservice::deleteFixture('customer_product2', true);

        Magento_Test_Webservice::deleteFixture('order', true);
        Magento_Test_Webservice::deleteFixture('quote', true);
        Magento_Test_Webservice::deleteFixture('product1', true);
        Magento_Test_Webservice::deleteFixture('product2', true);

        parent::tearDown();
    }

    /**
     * Test get order addresses for customer
     *
     *  @magentoDataFixture Api2/Sales/_fixtures/order_customer_address.php
     */
    public function testGetAddress()
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $this->getFixture('customer_order');

        //test billing
        $restResponse = $this->callGet('orders/' . $order->getId() . '/addresses/billing');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $this->assertEquals(
            $order->getBillingAddress()->getEmail(),
            $responseData['email']
        );

        //test shipping
        $restResponse = $this->callGet('orders/' . $order->getId() . '/addresses/shipping');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $this->assertEquals(
            $order->getShippingAddress()->getEmail(),
            $responseData['email']
        );
    }

    /**
     * Test retrieving address for not existing order
     */
    public function testGetAddressForUnavailableOrder()
    {
        $restResponse = $this->callGet('orders/invalid_id/address/billing');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        $restResponse = $this->callGet('orders/invalid_id/address/shipping');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test get order address for admin
     *
     * @magentoDataFixture Api2/Sales/_fixtures/order_customer_address.php
     */
    public function testGetOrderAddresses()
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $this->getFixture('customer_order');

        $restResponse = $this->callGet('orders/' . $order->getId() . '/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $this->assertCount(2, $responseData);

        $addressByType = array();
        foreach ($responseData as $address) {
            $type = $address['address_type'];
            $addressByType[$type] = $address;
        }

        $this->assertEquals(
            $order->getShippingAddress()->getEmail(),
            $addressByType[Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING]['email']
        );

        $this->assertEquals(
            $order->getBillingAddress()->getEmail(),
            $addressByType[Mage_Customer_Model_Address_Abstract::TYPE_BILLING]['email']
        );



    }

    /**
     * Test retrieving address for not existing order
     */
    public function testGetAddressesForUnavailableOrder()
    {
        $restResponse = $this->callGet('orders/invalid_id/addresses');

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test get order items if customer is not owner
     *
     * @magentoDataFixture Api2/Sales/_fixtures/order.php
     */
    public function testGetOrderIfCustomerIsNotOwner()
    {
        /* @var $fixtureOrder Mage_Sales_Model_Order */
        $fixtureOrder = $this->getFixture('order');
        $restResponse = $this->callGet('orders/' . $fixtureOrder->getId() . '/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }
}
