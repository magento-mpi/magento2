<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Usa_Model_Shipping_Carrier_UpsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Usa_Model_Shipping_Carrier_Ups
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_Usa_Model_Shipping_Carrier_Ups');
    }

    public function testGetShipAcceptUrl()
    {
        $this->assertEquals($this->_model->getShipAcceptUrl(), 'https://wwwcie.ups.com/ups.app/xml/ShipAccept');
    }

    /**
     * Test ship accept url for live site
     *
     * @magentoConfigFixture current_store carriers/ups/is_account_live 1
     */
    public function testGetShipAcceptUrlLive()
    {
        $this->assertEquals($this->_model->getShipAcceptUrl(), 'https://onlinetools.ups.com/ups.app/xml/ShipAccept');
    }

    public function testGetShipConfirmUrl()
    {
        $this->assertEquals($this->_model->getShipConfirmUrl(), 'https://wwwcie.ups.com/ups.app/xml/ShipConfirm');
    }

    /**
     * Test ship accept url for live site
     *
     * @magentoConfigFixture current_store carriers/ups/is_account_live 1
     */
    public function testGetShipConfirmUrlLive()
    {
        $this->assertEquals($this->_model->getShipConfirmUrl(), 'https://onlinetools.ups.com/ups.app/xml/ShipConfirm');
    }
}
