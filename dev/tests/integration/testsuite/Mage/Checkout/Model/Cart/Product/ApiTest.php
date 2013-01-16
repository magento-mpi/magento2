<?php
/**
 * Checkout Cart Product API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Checkout_Model_Cart_Product_ApiTest extends Mage_Checkout_Model_Cart_AbstractTest
{
    /**
     * Test quote item update.
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_simple_product.php
     */
    public function testUpdate()
    {
        $quote = $this->_getQuote();
        $quoteItems = $quote->getAllItems();
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = reset($quoteItems);
        $this->assertEquals(1, $quoteItem->getQty(), 'Quote item should have qty = 1.');

        $qtyToUpdate = 5;
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartProductUpdate',
            array(
                'quoteId' => $quote->getId(),
                'productsData' => array(
                    array(
                        'sku' => 'simple',
                        'qty' => $qtyToUpdate,
                    )
                )
            )
        );

        $this->assertTrue($soapResult, 'Error during product update in cart via API call');
        /** @var Mage_Sales_Model_Quote $quoteAfterUpdate */
        $quoteAfterUpdate = Mage::getModel('Mage_Sales_Model_Quote');
        $quoteAfterUpdate->load($quote->getId());
        $quoteItemsUpdated = $quoteAfterUpdate->getAllItems();
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItemUpdated = reset($quoteItemsUpdated);
        $this->assertEquals($qtyToUpdate, $quoteItemUpdated->getQty(), 'Incorrect quote item quantity after update.');
    }

    /**
     * Test quote item remove.
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_simple_product.php
     */
    public function testRemove()
    {
        $quote = $this->_getQuote();
        $this->assertCount(1, $quote->getAllItems(), 'Quote should have exactly 1 item.');

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartProductRemove',
            array(
                'quoteId' => $quote->getId(),
                'productsData' => array(
                    array(
                        'sku' => 'simple',
                    )
                )
            )
        );

        $this->assertTrue($soapResult, 'Error during product remove from cart via API call');
        /** @var Mage_Sales_Model_Quote $quoteAfterUpdate */
        $quoteAfterUpdate = Mage::getModel('Mage_Sales_Model_Quote');
        $quoteAfterUpdate->load($quote->getId());
        $this->assertCount(0, $quoteAfterUpdate->getAllItems(), 'Quote item was not deleted.');
    }
}
