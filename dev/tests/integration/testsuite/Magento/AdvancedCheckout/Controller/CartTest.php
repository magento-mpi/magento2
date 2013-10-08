<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\AdvancedCheckout\Controller\Cart
 */
namespace Magento\AdvancedCheckout\Controller;

class CartTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * Test for \Magento\AdvancedCheckout\Controller\Cart::configureAction() with gift card product
     *
     * @magentoDataFixture Magento/AdvancedCheckout/_files/quote_with_gift_card_product.php
     */
    public function testConfigureActionWithGiftCardProduct()
    {
        /** @var $session \Magento\Checkout\Model\Session  */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Checkout\Model\Session');

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 1);

        $this->dispatch('checkout/cart/configure/id/' . $quoteItem->getId());
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            \Magento\Core\Model\Message::ERROR,
            'Magento\Checkout\Model\Session'
        );

        $this->assertSelectCount('button[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('input#giftcard-amount-input[type="text"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain gift card amount input field');
    }

    /**
     * Test for \Magento\AdvancedCheckout\Controller\Cart::configureFailedAction() with simple product
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testConfigureFailedActionWithSimpleProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            \Magento\Core\Model\Message::ERROR,
            'Magento\Checkout\Model\Session'
        );

        $this->assertSelectCount('button[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for simple product doesn\'t contain "Update Cart" button');
    }

    /**
     * Test for \Magento\AdvancedCheckout\Controller\Cart::configureFailedAction() with bundle product
     *
     * @magentoDataFixture Magento/Bundle/_files/product.php
     */
    public function testConfigureFailedActionWithBundleProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/3');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            \Magento\Core\Model\Message::ERROR,
            'Magento\Checkout\Model\Session'
        );

        $this->assertSelectCount('button[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for bundle product doesn\'t contain "Update Cart" button');
    }

    /**
     * Test for \Magento\AdvancedCheckout\Controller\Cart::configureFailedAction() with downloadable product
     *
     * @magentoDataFixture Magento/Downloadable/_files/product.php
     */
    public function testConfigureFailedActionWithDownloadableProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            \Magento\Core\Model\Message::ERROR,
            'Magento\Checkout\Model\Session'
        );

        $this->assertSelectCount('button[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for downloadable product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('#downloadable-links-list', 1, $response->getBody(),
            'Response for downloadable product doesn\'t contain links for download');
    }

    /**
     * Test for \Magento\AdvancedCheckout\Controller\Cart::configureFailedAction() with configurable product
     *
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     */
    public function testConfigureFailedActionWithConfigurableProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            \Magento\Core\Model\Message::ERROR,
            'Magento\Checkout\Model\Session'
        );

        $this->assertSelectCount('button[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for configurable product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('select.super-attribute-select', 1, $response->getBody(),
            'Response for configurable product doesn\'t contain select for super attribute');
    }

    /**
     * Test for \Magento\AdvancedCheckout\Controller\Cart::configureFailedAction() with gift card product
     *
     * @magentoDataFixture Magento/GiftCard/_files/gift_card.php
     */
    public function testConfigureFailedActionWithGiftCardProduct()
    {
        $this->dispatch('checkout/cart/configureFailed/id/1');
        $response = $this->getResponse();

        $this->assertSessionMessages(
            $this->isEmpty(),
            \Magento\Core\Model\Message::ERROR,
            'Magento\Checkout\Model\Session'
        );

        $this->assertSelectCount('button[type="button"][title="Update Cart"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain "Update Cart" button');

        $this->assertSelectCount('input#giftcard-amount-input[type="text"]', 1, $response->getBody(),
            'Response for gift card product doesn\'t contain gift card amount input field');
    }

    /**
     * Gets \Magento\Sales\Model\Quote\Item from \Magento\Sales\Model\Quote by product id
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param $productId
     * @return \Magento\Sales\Model\Quote\Item|null
     */
    private function _getQuoteItemIdByProductId($quote, $productId)
    {
        /** @var $quoteItems \Magento\Sales\Model\Quote\Item[] */
        $quoteItems = $quote->getAllItems();
        foreach ($quoteItems as $quoteItem) {
            if ($productId == $quoteItem->getProductId()) {
                return $quoteItem;
            }
        }
        return null;
    }
}
