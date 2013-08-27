<?php
/**
 * Test case for testing operations with gift card in shopping card via API.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Enterprise/GiftCardAccount/_files/giftcardaccount.php
 * @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product.php
 */
class Enterprise_GiftCard_Model_Checkout_Cart_ApiTest extends PHPUnit_Framework_TestCase
{
    const GIFT_CARD_ACCOUNT_CODE = 'giftcardaccount_fixture';

    /**
     * Test apply gift card to shopping cart via API.
     */
    public function testAdd()
    {
        $quote = $this->_getQuote();
        $giftCardAccountCode = self::GIFT_CARD_ACCOUNT_CODE;
        $isAdded = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartGiftcardAdd',
            array(
                $giftCardAccountCode,
                $quote->getId()
            )
        );
        $this->assertTrue($isAdded, "Gift card was not applied to shopping cart.");
        $giftCards = $this->_loadGiftCards($quote->getId());
        $this->assertCount(1, $giftCards, "Exactly 1 gift card must be applied to the shopping cart.");
        $this->assertEquals($giftCardAccountCode, $giftCards[0]['c']);
    }

    /**
     * Test retrieving the list of all gift cards applied to shopping cart.
     *
     * @depends testAdd
     */
    public function testList()
    {
        $giftCards = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartGiftcardList',
            array(
                $this->_getQuote()->getId()
            )
        );
        $this->assertInternalType('array', $giftCards, 'The list of gift cards must be an array.');
        $this->assertCount(1, $giftCards, 'There must be exactly 1 gift card applied to current quote.');

        $giftCardData = reset($giftCards);
        $this->assertGreaterThan(0, (int)$giftCardData['giftcardaccount_id'], 'Gift card account ID value is invalid.');
        $expectedGiftCardData = array(
            'giftcardaccount_id' => $giftCardData['giftcardaccount_id'],
            'code' => self::GIFT_CARD_ACCOUNT_CODE,
            'used_amount' => '9.9900',
            'base_amount' => '9.9900',
        );
        $this->assertEquals($expectedGiftCardData, $giftCardData, 'Gift card data is invalid.');
    }

    /**
     * Test removing gift card from the list of those which are used in shopping cart.
     *
     * @depends testList
     */
    public function testRemove()
    {
        $quote = $this->_getQuote();
        $isRemoved = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartGiftcardRemove',
            array(
                self::GIFT_CARD_ACCOUNT_CODE,
                $quote->getId()
            )
        );
        $this->assertTrue($isRemoved, "Gift card was not unattached from the shopping cart.");
        /** Check if gift card was actually unassigned. */
        $giftCards = $this->_loadGiftCards($quote->getId());
        $this->assertCount(0, $giftCards, "No gift cards must be applied to the shopping cart.");
    }

    /**
     * Load the list of gift cards assigned to the quote with specified ID.
     *
     * @param int $quoteId
     * @return array
     */
    protected function _loadGiftCards($quoteId)
    {
        /** @var Magento_Sales_Model_Quote $updatedQuote */
        $updatedQuote = Mage::getModel('Magento_Sales_Model_Quote')->load($quoteId);
        $this->assertInternalType(
            'string',
            $updatedQuote->getGiftCards(),
            "'gift_cards' property contains invalid value."
        );
        $giftCards = unserialize($updatedQuote->getGiftCards());
        $this->assertInternalType(
            'array',
            $giftCards,
            "The value of 'gift_cards' property must contain serialized array."
        );
        return $giftCards;
    }

    /**
     * Retrieve quote created in fixture.
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        /** @var $session Magento_Checkout_Model_Session */
        $session = Mage::getModel('Magento_Checkout_Model_Session');
        /** @var Magento_Sales_Model_Quote $quote */
        $quote = $session->getQuote();
        return $quote;
    }
}
