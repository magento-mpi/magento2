<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Usa_Model_Shipping_Carrier_UpsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Usa_Model_Shipping_Carrier_Ups
     */
    private $_object;

    public function setUp()
    {
        $simplexmlFactory = $this->getMock('Magento_Usa_Model_Simplexml_ElementFactory', array(), array(), '', false);
        $storeConfig = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Store_Config');
        /** @var $simplexmlFactory Magento_Usa_Model_Simplexml_ElementFactory */
        $this->_object = new Magento_Usa_Model_Shipping_Carrier_Ups($storeConfig, $simplexmlFactory);
    }

    public function testGetShipAcceptUrl()
    {
        $this->assertEquals($this->_object->getShipAcceptUrl(), 'https://wwwcie.ups.com/ups.app/xml/ShipAccept');
    }

    /**
     * Test ship accept url for live site
     *
     * @magentoConfigFixture current_store carriers/ups/is_account_live 1
     */
    public function testGetShipAcceptUrlLive()
    {
        $this->assertEquals($this->_object->getShipAcceptUrl(), 'https://onlinetools.ups.com/ups.app/xml/ShipAccept');
    }

    public function testGetShipConfirmUrl()
    {
        $this->assertEquals($this->_object->getShipConfirmUrl(), 'https://wwwcie.ups.com/ups.app/xml/ShipConfirm');
    }

    /**
     * Test ship accept url for live site
     *
     * @magentoConfigFixture current_store carriers/ups/is_account_live 1
     */
    public function testGetShipConfirmUrlLive()
    {
        $this->assertEquals($this->_object->getShipConfirmUrl(), 'https://onlinetools.ups.com/ups.app/xml/ShipConfirm');
    }
}
