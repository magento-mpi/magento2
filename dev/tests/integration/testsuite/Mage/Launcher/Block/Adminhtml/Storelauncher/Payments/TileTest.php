<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_Tile
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_TileTest extends Mage_Backend_Area_TestCase
{
    /**
     * Tile block
     *
     * @var Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_Tile
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::getObjectManager()->create('Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_Tile');
    }

    protected function tearDown()
    {
        // @TODO: App isolation doesn't clear config cache
        Mage::getConfig()->removeCache();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store payment/paypal_express/active 1
     */
    public function testGetConfiguredMethodsForPayPalExpress()
    {
        $this->assertEquals(array('PayPal Express Checkout'), $this->_block->getConfiguredMethods());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store payment/paypal_standard/active 1
     */
    public function testGetConfiguredMethodsForPayPalStandard()
    {
        $this->assertEquals(array('PayPal Standard'), $this->_block->getConfiguredMethods());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store payment/payflow_advanced/active 1
     */
    public function testGetConfiguredMethodsForPayflowAdvanced()
    {
        $this->assertEquals(array('PayPal Payments Advanced'), $this->_block->getConfiguredMethods());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store payment/paypal_direct/active 1
     */
    public function testGetConfiguredMethodsForPayPalPaymentsPro()
    {
        $this->assertEquals(array('PayPal Payments Pro'), $this->_block->getConfiguredMethods());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store payment/payflow_link/active 1
     */
    public function testGetConfiguredMethodsForPayflowLink()
    {
        $this->assertEquals(array('PayPal Payflow Link'), $this->_block->getConfiguredMethods());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store payment/verisign/active 1
     */
    public function testGetConfiguredMethodsForPayflowPro()
    {
        $this->assertEquals(array('PayPal Payflow Pro'), $this->_block->getConfiguredMethods());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default_store payment/authorizenet/active 1
     */
    public function testGetConfiguredMethodsForAuthorizenet()
    {
        $this->assertEquals(array('Authorize.net'), $this->_block->getConfiguredMethods());
    }
}