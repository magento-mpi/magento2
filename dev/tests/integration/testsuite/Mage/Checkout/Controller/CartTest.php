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
 * Test class for Mage_Checkout_Controller_Cart
 */
class Mage_Checkout_Controller_CartTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Test for Mage_Checkout_Controller_Cart::configureAction() with simple product
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_simple_product.php
     */
    public function testConfigureActionWithSimpleProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 1);
        $this->assertNotNull($quoteItem, 'Cannot get quote item for simple product');

        $this->dispatch('checkout/cart/configure/id/' . $quoteItem->getId());
        $response = $this->getResponse();

        $this->assertSessionMessages($this->isEmpty(), Mage_Core_Model_Message::ERROR, 'Mage_Checkout_Model_Session');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for simple product doesn\'t contain "Update Cart" button');
    }

    /**
     * Test for Mage_Checkout_Controller_Cart::configureAction() with simple product and custom option
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_simple_product_and_custom_option.php
     */
    public function testConfigureActionWithSimpleProductAndCustomOption()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 1);
        $this->assertNotNull($quoteItem, 'Cannot get quote item for simple product with custom option');

        $this->dispatch('checkout/cart/configure/id/' . $quoteItem->getId());
        $response = $this->getResponse();

        $this->assertSessionMessages($this->isEmpty(), Mage_Core_Model_Message::ERROR, 'Mage_Checkout_Model_Session');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for simple product with custom option doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('input.product-custom-option[type="text"]', 1, $response->getBody(),
            'Response for simple product with custom option doesn\'t contain custom option input field');
    }

    /**
     * Test for Mage_Checkout_Controller_Cart::configureAction() with bundle product
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_bundle_product.php
     */
    public function testConfigureActionWithBundleProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 3);
        $this->assertNotNull($quoteItem, 'Cannot get quote item for bundle product');

        $this->dispatch('checkout/cart/configure/id/' . $quoteItem->getId());
        $response = $this->getResponse();

        $this->assertSessionMessages($this->isEmpty(), Mage_Core_Model_Message::ERROR, 'Mage_Checkout_Model_Session');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for bundle product doesn\'t contain "Update Cart" button');
    }

    /**
     * Test for Mage_Checkout_Controller_Cart::configureAction() with downloadable product
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_downloadable_product.php
     */
    public function testConfigureActionWithDownloadableProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 1);
        $this->assertNotNull($quoteItem, 'Cannot get quote item for downloadable product');

        $this->dispatch('checkout/cart/configure/id/' . $quoteItem->getId());
        $response = $this->getResponse();

        $this->assertSessionMessages($this->isEmpty(), Mage_Core_Model_Message::ERROR, 'Mage_Checkout_Model_Session');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for downloadable product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('ul#downloadable-links-list.options-list', 1, $response->getBody(),
            'Response for downloadable product doesn\'t contain links for download');
    }

    /**
     * Test for Mage_Checkout_Controller_Cart::configureAction() with configurable product
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_configurable_product.php
     */
    public function testConfigureActionWithConfigurableProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 1);
        $this->assertNotNull($quoteItem, 'Cannot get quote item for configurable product');

        $this->dispatch('checkout/cart/configure/id/' . $quoteItem->getId());
        $response = $this->getResponse();

        $this->assertSessionMessages($this->isEmpty(), Mage_Core_Model_Message::ERROR, 'Mage_Checkout_Model_Session');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for configurable product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('select.super-attribute-select', 1, $response->getBody(),
            'Response for configurable product doesn\'t contain select for super attribute');
    }

    /**
     * Gets Mage_Sales_Model_Quote_Item from Mage_Sales_Model_Quote by product id
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param $productId
     * @return Mage_Sales_Model_Quote_Item|null
     */
    private function _getQuoteItemIdByProductId($quote, $productId)
    {
        /** @var $quoteItems Mage_Sales_Model_Quote_Item[] */
        $quoteItems = $quote->getAllItems();
        foreach ($quoteItems as $quoteItem) {
            if ($productId == $quoteItem->getProductId()) {
                return $quoteItem;
            }
        }
        return null;
    }
}
