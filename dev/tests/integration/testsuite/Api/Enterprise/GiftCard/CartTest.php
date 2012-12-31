<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @magentoDataFixture Api/Enterprise/GiftCard/_fixture/code_pool.php
 * @magentoDataFixture Api/Enterprise/GiftCard/_fixture/giftcard_account.php
 */
class Enterprise_GiftCard_CartTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test giftcard Shopping Cart add, list, remove
     *
     * @return void
     */
    public function testLSD()
    {
        //Test giftcard add to quote
        $storeId = 1;
        $quoteId = Magento_Test_Helper_Api::call($this, 'shoppingCartCreate', array('store' => $storeId));

        $giftcardAccountCode = 'giftcardaccount_fixture';
        $addResult = Magento_Test_Helper_Api::call($this,
            'shoppingCartGiftcardAdd',
            array(
                'giftcardAccountCode' => $giftcardAccountCode,
                'quoteId' => $quoteId,
                'storeId' => $storeId
            )
        );
        $this->assertTrue($addResult, 'Add giftcard to quote');

        //Test list of giftcards added to quote
        $giftCards = Magento_Test_Helper_Api::call(
            $this, 'shoppingCartGiftcardList',
            array('quoteId' => $quoteId, 'storeId' => $storeId)
        );
        $this->assertInternalType('array', $giftCards);
        $this->assertGreaterThan(0, count($giftCards));

        if (!isset($giftCards[0])) { // workaround for WSI plain array response
            $giftCards = array($giftCards);
        }
        $this->assertEquals($giftcardAccountCode, $giftCards[0]['code']);
        $this->assertEquals(9.99, $giftCards[0]['base_amount']);

        //Test giftcard removing from quote
        $removeResult = Magento_Test_Helper_Api::call($this,
            'shoppingCartGiftcardRemove',
            array(
                'giftcardAccountCode' => $giftcardAccountCode,
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
        $this->setExpectedException('SoapFault');
        Magento_Test_Helper_Api::call($this,
            'shoppingCartGiftcardRemove',
            array('giftcardAccountCode' => $giftcardAccountCode, 'quoteId' => $quoteId, 'storeId' => $storeId)
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
        $invalidData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalid_create);
        Magento_Test_Helper_Api::call($this, 'shoppingCartGiftcardAdd', (array)$invalidData);
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
        $invalidData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalid_list);
        Magento_Test_Helper_Api::call($this, 'shoppingCartGiftcardList', (array)$invalidData);
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
        $invalidData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalid_remove);
        Magento_Test_Helper_Api::call($this, 'shoppingCartGiftcardRemove', (array)$invalidData);
    }
}
