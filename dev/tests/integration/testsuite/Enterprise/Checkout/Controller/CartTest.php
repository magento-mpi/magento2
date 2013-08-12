<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_Checkout_Controller_Cart
 */
class Enterprise_Checkout_Controller_CartTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Test for Enterprise_Checkout_Controller_Cart::configureAction() with gift card product
     *
     * @magentoDataFixture Enterprise/Checkout/_files/quote_with_gift_card_product.php
     */
    public function testConfigureActionWithGiftCardProduct()
    {
        /** @var $session Magento_Checkout_Model_Session  */
        $session = Mage::getModel('Magento_Checkout_Model_Session');

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 1);

        $this->dispatch('checkout/cart/configure/id/' . $quoteItem->getId());
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            Magento_Core_Model_Message::ERROR,
            'Magento_Checkout_Model_Session'
        );

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('input#giftcard-amount-input[type="text"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain gift card amount input field');
    }

    /**
     * Test for Enterprise_Checkout_Controller_Cart::configureFailedAction() with simple product
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testConfigureFailedActionWithSimpleProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            Magento_Core_Model_Message::ERROR,
            'Magento_Checkout_Model_Session'
        );

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for simple product doesn\'t contain "Update Cart" button');
    }

    /**
     * Test for Enterprise_Checkout_Controller_Cart::configureFailedAction() with bundle product
     *
     * @magentoDataFixture Magento/Bundle/_files/product.php
     */
    public function testConfigureFailedActionWithBundleProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/3');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            Magento_Core_Model_Message::ERROR,
            'Magento_Checkout_Model_Session'
        );

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for bundle product doesn\'t contain "Update Cart" button');
    }

    /**
     * Test for Enterprise_Checkout_Controller_Cart::configureFailedAction() with downloadable product
     *
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     */
    public function testConfigureFailedActionWithDownloadableProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            Magento_Core_Model_Message::ERROR,
            'Magento_Checkout_Model_Session'
        );

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for downloadable product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('ul#downloadable-links-list.options-list', 1, $response->getBody(),
            'Response for downloadable product doesn\'t contain links for download');
    }

    /**
     * Test for Enterprise_Checkout_Controller_Cart::configureFailedAction() with configurable product
     *
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     */
    public function testConfigureFailedActionWithConfigurableProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            Magento_Core_Model_Message::ERROR,
            'Magento_Checkout_Model_Session'
        );

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for configurable product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('select.super-attribute-select', 1, $response->getBody(),
            'Response for configurable product doesn\'t contain select for super attribute');
    }

    /**
     * Test for Enterprise_Checkout_Controller_Cart::configureFailedAction() with gift card product
     *
     * @magentoDataFixture Enterprise/GiftCard/_files/gift_card.php
     */
    public function testConfigureFailedActionWithGiftCardProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            Magento_Core_Model_Message::ERROR,
            'Magento_Checkout_Model_Session'
        );

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('input#giftcard-amount-input[type="text"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain gift card amount input field');
    }

    /**
     * Gets Magento_Sales_Model_Quote_Item from Magento_Sales_Model_Quote by product id
     *
     * @param Magento_Sales_Model_Quote $quote
     * @param $productId
     * @return Magento_Sales_Model_Quote_Item|null
     */
    private function _getQuoteItemIdByProductId($quote, $productId)
    {
        /** @var $quoteItems Magento_Sales_Model_Quote_Item[] */
        $quoteItems = $quote->getAllItems();
        foreach ($quoteItems as $quoteItem) {
            if ($productId == $quoteItem->getProductId()) {
                return $quoteItem;
            }
        }
        return null;
    }
}
