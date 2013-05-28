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
     * @magentoDataFixture Saas/Launcher/controllers/_files/products_nonsupported.php
     */
    public function testDisabledProducts()
    {
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 0);
        $this->dispatch('launcher/testtransaction');
        $this->assertRedirect($this->stringContains('checkout/cart'));
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 0);
        $this->assertSessionMessages(
            $this->equalTo(array("You need to have at least one Simple or Virtual Product to run test transaction.")),
            Mage_Core_Model_Message::NOTICE,
            'Mage_Checkout_Model_Session'
        );
    }

    /**
     * @magentoDataFixture Saas/Launcher/controllers/_files/product_simple.php
     */
    public function testEnabledProducts()
    {
        $this->dispatch('launcher/testtransaction');
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 1);
        $this->dispatch('launcher/testtransaction');
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 1);
        $this->assertRedirect($this->stringContains('checkout/cart'));
    }

    /**
     * @magentoDataFixture Saas/Launcher/controllers/_files/product_virtual.php
     */
    public function testVirtualProducts()
    {
        $this->dispatch('launcher/testtransaction');
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 1);
        $this->dispatch('launcher/testtransaction');
        $this->assertEquals($this->_cart->getQuote()->getItemsQty(), 1);
        $this->assertRedirect($this->stringContains('checkout/cart'));
    }
}
