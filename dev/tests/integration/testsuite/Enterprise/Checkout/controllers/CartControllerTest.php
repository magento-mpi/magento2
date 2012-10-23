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
 * Test class for Enterprise_Checkout_CartController
 */
class Enterprise_Checkout_CartControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Test for Enterprise_Checkout_CartController::configureAction() with gift card product
     *
     * @magentoDataFixture Enterprise/Checkout/_files/quote_with_gift_card_product.php
     */
    public function testConfigureActionWithGiftCardProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 1);

        $this->dispatch('checkout/cart/configure/id/' . $quoteItem->getId());
        $response = $this->getResponse();

        $this->assertCount(0, $session->getMessages()->getErrors(),
            'Response for gift card product contains errors');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('input#giftcard_amount_input[type="text"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain gift card amount input field');
    }

    /**
     * Test for Enterprise_Checkout_CartController::configureFailedAction() with simple product
     *
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testConfigureFailedActionWithSimpleProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertCount(0, $session->getMessages()->getErrors(),
            'Response for simple product contains errors');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for simple product doesn\'t contain "Update Cart" button');
    }

    /**
     * Test for Enterprise_Checkout_CartController::configureFailedAction() with bundle product
     *
     * @magentoDataFixture Mage/Bundle/_files/product.php
     */
    public function testConfigureFailedActionWithBundleProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $this->dispatch('checkout/cart/configureFailed/id/3');
        $response = $this->getResponse();

        $this->assertCount(0, $session->getMessages()->getErrors(),
            'Response for bundle product contains errors');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for bundle product doesn\'t contain "Update Cart" button');
    }

    /**
     * Test for Enterprise_Checkout_CartController::configureFailedAction() with downloadable product
     *
     * @magentoDataFixture Mage/Downloadable/_files/product.php
     */
    public function testConfigureFailedActionWithDownloadableProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertCount(0, $session->getMessages()->getErrors(),
            'Response for downloadable product contains errors');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for downloadable product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('ul#downloadable-links-list.options-list', 1, $response->getBody(),
            'Response for downloadable product doesn\'t contain links for download');
    }

    /**
     * Test for Enterprise_Checkout_CartController::configureFailedAction() with configurable product
     *
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     */
    public function testConfigureFailedActionWithConfigurableProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertCount(0, $session->getMessages()->getErrors(),
            'Response for configurable product contains errors');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for configurable product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('select.super-attribute-select', 1, $response->getBody(),
            'Response for configurable product doesn\'t contain select for super attribute');
    }

    /**
     * Test for Enterprise_Checkout_CartController::configureFailedAction() with gift card product
     *
     * @magentoDataFixture Enterprise/GiftCard/_files/gift_card.php
     */
    public function testConfigureFailedActionWithGiftCardProduct()
    {
        /** @var $session Mage_Checkout_Model_Session  */
        $session = Mage::getModel('Mage_Checkout_Model_Session');

        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertCount(0, $session->getMessages()->getErrors(),
            'Response for gift card product contains errors');

        $this->assertSelectCount('button.button.btn-cart[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('input#giftcard_amount_input[type="text"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain gift card amount input field');
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
