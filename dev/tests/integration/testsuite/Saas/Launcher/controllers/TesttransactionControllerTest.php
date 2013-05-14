<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_TesttransactionControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Checkout_Model_Cart
     */
    protected $_cart;

    public function setUp()
    {
        parent::setUp();
        $this->_cart = $this->_objectManager->get('Mage_Checkout_Model_Cart');
    }

    public function testEmptyStore()
    {
        $this->dispatch('launcher/testtransaction');
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 0);
        $this->assertRedirect($this->stringContains('checkout/cart'));
    }

    /**
     * @magentoDataFixture Saas/Launcher/controllers/_files/product_disabled.php
     */
    public function testDisabledProducts()
    {
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 0);
        $this->dispatch('launcher/testtransaction');
        $this->assertRedirect($this->stringContains('checkout/cart'));
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 0);
    }

    /**
     * @magentoDataFixture Saas/Launcher/controllers/_files/product_enabled.php
     */
    public function testEnabledProducts()
    {
        $this->dispatch('launcher/testtransaction');
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 1);
        $this->dispatch('launcher/testtransaction');
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 1);
        $this->assertRedirect($this->stringContains('checkout/cart'));
    }
}
