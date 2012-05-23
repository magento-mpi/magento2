<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_ProductController.
 */
class Mage_Checkout_CartControllerTest extends Magento_Test_TestCase_ControllerAbstract
{

    /**
     * Test for Mage_Catalog_ProductController::configureAction()
     *
     * Quote consists of simple product with custom option
     * @magentoDataFixture Mage/Checkout/_files/product_with_custom_option.php
     */
    public function testConfigureActionWithCustomOption()
    {
        $quoteItemId = Mage::registry('product_with_custom_option/quoteItemId');
        $this->dispatch('checkout/cart/configure/id/' . $quoteItemId);
        $out = $this->getResponse()->getBody();
        $this->assertContains('<button type="button" title="Update Cart" class="button btn-cart"', $out);
    }

    /**
     * Test for Mage_Catalog_ProductController::configureAction()
     *
     * Quote consists of simple product
     * @magentoDataFixture Mage/Checkout/_files/product.php
     */
    public function testConfigureAction()
    {
        $quoteItemId = Mage::registry('product/quoteItemId');
        $this->dispatch('checkout/cart/configure/id/' . $quoteItemId);
        $out = $this->getResponse()->getBody();
        $this->assertContains('<button type="button" title="Update Cart" class="button btn-cart"', $out);
    }
}
