<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @magentoApiDataFixture Api/GiftCard/_fixture/code_pool.php
 * @magentoApiDataFixture Api/GiftCard/_fixture/giftcard_account.php
 */
class Api_GiftCard_CartTest extends Magento_Test_TestCase_ApiAbstract
{
    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->deleteFixture('giftcard_account', true);

        parent::tearDown();
    }

    /**
     * Test giftcard Shopping Cart add, list, remove
     *
     * @return void
     */
    public function testLSD()
    {
        //Test giftcard add to quote
        $giftCardAccount = self::getFixture('giftcard_account');
        $storeId = 1;
        $quoteId = $this->call('cart.create', array('store' => $storeId));

        $addResult = $this->call(
            'cart_giftcard.add',
            array(
                'giftcardAccountCode' => $giftCardAccount->getCode(),
                'quoteId' => $quoteId,
                'storeId' => $storeId
            )
        );
        $this->assertTrue($addResult, 'Add giftcard to quote');

        //Test list of giftcards added to quote
        $giftCards = $this->call('cart_giftcard.list', array('quoteId' => $quoteId, 'storeId' => $storeId));
        $this->assertInternalType('array', $giftCards);
        $this->assertGreaterThan(0, count($giftCards));

        if (!isset($giftCards[0])) { // workaround for WSI plain array response
            $giftCards = array($giftCards);
        }
        $this->assertEquals($giftCardAccount->getCode(), $giftCards[0]['code']);
        $this->assertEquals($giftCardAccount->getBalance(), $giftCards[0]['base_amount']);

        //Test giftcard removing from quote
        $removeResult = $this->call(
            'cart_giftcard.remove',
            array(
                'giftcardAccountCode' => $giftCardAccount->getCode(),
                'quoteId' => $quoteId,
                'storeId' => $storeId
            )
        );
        $this->assertTrue($removeResult, 'Remove giftcard from quote');

        // remove quote
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('Mage_Sales_Model_Quote');
        $quote->setId($quoteId);
        $quote->delete();

        //Test giftcard removed
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call(
            'cart_giftcard.remove',
            array('giftcardAccountCode' => $giftCardAccount->getCode(), 'quoteId' => $quoteId, 'storeId' => $storeId)
        );
    }

    /**
     * Test add throw exception with incorrect data
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testIncorrectDataAddException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_create);
        $this->call('cart_giftcard.add', $invalidData);
    }

    /**
     * Test list throw exception with incorrect data
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testIncorrectDataListException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_list);
        $this->call('cart_giftcard.list', $invalidData);
    }

    /**
     * Test remove throw exception with incorrect data
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testIncorrectDataRemoveException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_remove);
        $this->call('cart_giftcard.remove', $invalidData);
    }
}
