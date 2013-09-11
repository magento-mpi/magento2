<?php
/**
 * Test case for gift message assigning to the shopping cart via API.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GiftMessage_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test setting gift message fot the whole shopping cart.
     *
     * @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSetForQuote()
    {
        /** Prepare data. */
        $quoteId = $this->_getQuote()->getId();

        /** Call tested method. */
        $status = Magento_TestFramework_Helper_Api::call(
            $this,
            'giftMessageSetForQuote',
            array($quoteId, $this->_getGiftMessageData())
        );
        $expectedStatus = array('entityId' => $quoteId, 'result' => true, 'error' => '');
        $this->assertEquals($expectedStatus, (array)$status, 'Gift message was not added to the quote.');

        /** Ensure that messages were actually added and saved to DB. */
        /** @var \Magento\Sales\Model\Quote $updatedQuote */
        $updatedQuote = Mage::getModel('Magento\Sales\Model\Quote')->load($quoteId);
        $this->assertGreaterThan(0, (int)$updatedQuote->getGiftMessageId(), "Gift message was not added.");
        $this->_checkCreatedGiftMessage($updatedQuote->getGiftMessageId(), $this->_getGiftMessageData());
    }

    /**
     * Test setting gift message fot the specific quote item.
     *
     * @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSetForQuoteItem()
    {
        /** Prepare data. */
        $quoteItems = $this->_getQuote()->getAllItems();
        /** @var \Magento\Sales\Model\Quote\Item $quoteItem */
        $quoteItem = reset($quoteItems);

        /** Call tested method. */
        $status = Magento_TestFramework_Helper_Api::call(
            $this,
            'giftMessageSetForQuoteItem',
            array($quoteItem->getId(), $this->_getGiftMessageData())
        );
        $expectedStatus = array('entityId' => $quoteItem->getId(), 'result' => true, 'error' => '');
        $this->assertEquals($expectedStatus, (array)$status, 'Gift message was not added to the quote.');

        /** Ensure that messages were actually added and saved to DB. */
        /** @var \Magento\Sales\Model\Quote\Item $updatedQuoteItem */
        $updatedQuoteItem = Mage::getModel('Magento\Sales\Model\Quote\Item')->load($quoteItem->getId());
        $this->assertGreaterThan(0, (int)$updatedQuoteItem->getGiftMessageId(), "Gift message was not added.");
        $this->_checkCreatedGiftMessage($updatedQuoteItem->getGiftMessageId(), $this->_getGiftMessageData());

    }

    /**
     * Test setting gift message fot the specified products in shopping cart.
     *
     * @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSetForQuoteProduct()
    {
        /** Prepare data. */
        $quoteItems = $this->_getQuote()->getAllItems();
        /** @var \Magento\Sales\Model\Quote\Item $quoteItem */
        $quoteItem = reset($quoteItems);

        /** Call tested method. */
        $status = Magento_TestFramework_Helper_Api::call(
            $this,
            'giftMessageSetForQuoteProduct',
            array(
                $this->_getQuote()->getId(),
                array(
                    (object)array(
                        'product' => (object)array('product_id' => $quoteItem->getProductId()),
                        'message' => $this->_getGiftMessageData()
                    )
                )
            )
        );
        $expectedStatus = array((object)array('entityId' => $quoteItem->getId(), 'result' => true, 'error' => ''));
        $this->assertEquals($expectedStatus, $status, 'Gift message was not added to the quote.');

        /** Ensure that messages were actually added and saved to DB. */
        /** @var \Magento\Sales\Model\Quote\Item $updatedQuoteItem */
        $updatedQuoteItem = Mage::getModel('Magento\Sales\Model\Quote\Item')->load($quoteItem->getId());
        $this->assertGreaterThan(0, (int)$updatedQuoteItem->getGiftMessageId(), "Gift message was not added.");
        $this->_checkCreatedGiftMessage($updatedQuoteItem->getGiftMessageId(), $this->_getGiftMessageData());
    }

    /**
     * Prepare gift message data for tests.
     *
     * @return object
     */
    protected function _getGiftMessageData()
    {
        $giftMessageData = (object)array(
            'from' => 'from@null.null',
            'to' => 'to@null.null',
            'message' => 'Gift message content.'
        );
        return $giftMessageData;
    }

    /**
     * Ensure that added gift message was successfully stored in DB.
     *
     * @param int $giftMessageId
     * @param object $giftMessageData
     */
    protected function _checkCreatedGiftMessage($giftMessageId, $giftMessageData)
    {
        $giftMessage = Mage::getModel('Magento\GiftMessage\Model\Message')->load($giftMessageId);
        $this->assertEquals($giftMessageData->message, $giftMessage['message'], 'Message stored in DB is invalid.');
        $this->assertEquals($giftMessageData->to, $giftMessage['recipient'], 'Recipient stored in DB is invalid.');
        $this->assertEquals($giftMessageData->from, $giftMessage['sender'], 'Sender stored in DB is invalid.');
    }

    /**
     * Retrieve quote created in fixture.
     *
     * @return \Magento\Sales\Model\Quote
     */
    protected function _getQuote()
    {
        /** @var $session \Magento\Checkout\Model\Session */
        $session = Mage::getModel('Magento\Checkout\Model\Session');
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $session->getQuote();
        return $quote;
    }
}
