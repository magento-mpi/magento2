<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Usa_Model_Shipping_Carrier_UpsTest extends PHPUnit_Framework_TestCase
{
    public function testGetShipAcceptUrl()
    {
        $object = new Mage_Usa_Model_Shipping_Carrier_Ups();
        $this->assertEquals($object->getShipAcceptUrl(), 'https://wwwcie.ups.com/ups.app/xml/ShipAccept');
    }

    /**
     * Test ship accept url for live site
     *
     * @magentoConfigFixture current_store carriers/ups/is_account_live 1
     */
    public function testGetShipAcceptUrlLive()
    {
        $object = new Mage_Usa_Model_Shipping_Carrier_Ups();
        $this->assertEquals($object->getShipAcceptUrl(), 'https://onlinetools.ups.com/ups.app/xml/ShipAccept');
    }

    public function testGetShipConfirmUrl()
    {
        $object = new Mage_Usa_Model_Shipping_Carrier_Ups();
        $this->assertEquals($object->getShipConfirmUrl(), 'https://wwwcie.ups.com/ups.app/xml/ShipConfirm');
    }

    /**
     * Test ship accept url for live site
     *
     * @magentoConfigFixture current_store carriers/ups/is_account_live 1
     */
    public function testGetShipConfirmUrlLive()
    {
        $object = new Mage_Usa_Model_Shipping_Carrier_Ups();
        $this->assertEquals($object->getShipConfirmUrl(), 'https://onlinetools.ups.com/ups.app/xml/ShipConfirm');
    }
}
