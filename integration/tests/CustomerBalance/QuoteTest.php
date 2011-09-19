<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CustomerBalance tests
 *
 * @category   Paas
 * @package    integration_tests
 * @author     Magento PaaS Team <paas-team@magento.com>
 */

/**
 * @magentoDataFixture CustomerBalance/_fixtures/Quote.php
 */
class CustomerBalance_QuoteTest extends Magento_Test_Webservice
{
    /**
     * Customer fixture
     * @var Mage_Customer_Model_Customer
     */
    public static $customer = null;

    /**
     * Product fixture
     * @var Mage_Catalog_Model_Product
     */
    public static $product = null;

    /**
     * Shopping cart fixture
     * @var Mage_Sales_Model_Quote
     */
    public static $quote = null;

    /**
     * Shopping cart created by guest fixture
     * @var Mage_Sales_Model_Quote
     */
    public static $guestQuote = null;

    /**
     * Test successful customer balance set amount to quote
     *
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmount()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForQuote.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $data['input']['quote_id'] = self::$quote->getId();

        $result = $this->call('storecredit_quote.setAmount', $data['input']);

        $this->assertEquals($data['expected']['used_amount'], $result, 'Used amount is invalid');
    }

    /**
     * Test successful customer balance remove amount from quote
     *
     * @depends testCustomerBalanceForQuoteSetAmount
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmount()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForQuote.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $data['input']['quote_id'] = self::$quote->getId();

        $this->assertTrue($this->call('storecredit_quote.removeAmount', $data['input'], 'Remove used amount fail'));

        $quote = new Mage_Sales_Model_Quote();
        $quote->load(self::$quote->getId());
        $this->assertEquals(0, $quote->getCustomerBalanceAmountUsed(), 'Used amount must be deleted');
    }

    /**
     * Test successful customer balance set amount to quote using quote_id only
     *
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountWithoutStoreId()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForQuote.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $result = $this->call('storecredit_quote.setAmount', array('quote_id' => self::$quote->getId()));

        $this->assertEquals($data['expected']['used_amount'], $result, 'Used amount is invalid');
    }

    /**
     * Test successful customer balance remove amount from quote using quote_id only
     *
     * @depends testCustomerBalanceForQuoteSetAmountWithoutStoreId
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmountWithoutStoreId()
    {
        $input = array('quote_id' => self::$quote->getId());
        $this->assertTrue($this->call('storecredit_quote.removeAmount', $input, 'Remove used amount fail'));

        $quote = new Mage_Sales_Model_Quote();
        $quote->load(self::$quote->getId());
        $this->assertEquals(0, $quote->getCustomerBalanceAmountUsed(), 'Used amount must be deleted');
    }

    /**
     * Test successful customer balance set amount to quote using store code
     *
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountUsingStoreCode()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForQuoteUsingStoreCode.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $data['input']['quote_id'] = self::$quote->getId();

        $result = $this->call('storecredit_quote.setAmount', $data['input']);

        $this->assertEquals($data['expected']['used_amount'], $result, 'Used amount is invalid');
    }

    /**
     * Test successful customer balance remove amount from quote using store code
     *
     * @depends testCustomerBalanceForQuoteSetAmountUsingStoreCode
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmountUsingStoreCode()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForQuoteUsingStoreCode.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $data['input']['quote_id'] = self::$quote->getId();

        $this->assertTrue($this->call('storecredit_quote.removeAmount', $data['input'], 'Remove used amount fail'));

        $quote = new Mage_Sales_Model_Quote();
        $quote->load(self::$quote->getId());
        $this->assertEquals(0, $quote->getCustomerBalanceAmountUsed(), 'Used amount must be deleted');
    }

    /**
     * Test customer balance set amount to quote using store code exception: No store found with requested id or code.
     *
     * @expectedException Exception
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountUsingInvalidStoreCodeException()
    {
        $quoteFixture = simplexml_load_file(
            dirname(__FILE__) . '/_fixtures/CustomerBalanceForQuoteUsingInvalidStoreCode.xml'
        );
        $data = self::simpleXmlToArray($quoteFixture);

        $data['input']['quote_id'] = self::$quote->getId();

        $this->call('storecredit_quote.setAmount', $data['input']);
    }

    /**
     * Test customer balance set amount to quote exception:  No quote found with requested id.
     *
     * @expectedException Exception
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountExceptionQuoteNotExists()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForQuote.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $this->call('storecredit_quote.setAmount', $data['input']);
    }

    /**
     * Test customer balance remove amount from quote exception: No quote found with requested id.
     *
     * @expectedException Exception
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmountExceptionQuoteNotExists()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForQuote.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $this->call('storecredit_quote.removeAmount', $data['input']);
    }

    /**
     * Test customer balance set amount to quote exception:
     * Store credit can not be used for quote created by guest.
     *
     * @expectedException Exception
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmountExceptionGuestQuote()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForGuestQuote.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $data['input']['quote_id'] = self::$guestQuote->getId();

        $this->call('storecredit_quote.setAmount', $data['input']);
    }

    /**
     * Test customer balance remove amount from quote exception:
     * Store credit can not be used for quote created by guest.
     *
     * @expectedException Exception
     * @return void
     */
    public function testCustomerBalanceForQuoteRemoveAmountExceptionGuestQuote()
    {
        $quoteFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/CustomerBalanceForGuestQuote.xml');
        $data = self::simpleXmlToArray($quoteFixture);

        $data['input']['quote_id'] = self::$guestQuote->getId();

        $this->call('storecredit_quote.removeAmount', $data['input'], 'Remove used amount fail');
    }

    public static function tearDownAfterClass()
    {
        Mage::register('isSecureArea', true);

        self::$guestQuote->delete();
        self::$quote->delete();
        self::$customer->delete();
        self::$product->delete();

        self::$guestQuote = null;
        self::$quote = null;
        self::$customer = null;
        self::$product = null;

        Mage::unregister('isSecureArea');
    }
}
