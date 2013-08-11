<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Controller_TestTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testTransaction()
    {
        $cart = $this->_objectManager->create('Mage_Checkout_Model_Cart');
        $this->assertEquals($cart->getQuote()->getItemsQty(), 0);
        $this->dispatch('launcher/test/transaction');
        $this->assertRedirect($this->stringContains('checkout/cart'));
        $this->assertEquals($cart->getQuote()->getItemsQty(), 1);
        $this->dispatch('launcher/test/transaction');
        $this->assertEquals($cart->getQuote()->getItemsQty(), 1);
    }
}
