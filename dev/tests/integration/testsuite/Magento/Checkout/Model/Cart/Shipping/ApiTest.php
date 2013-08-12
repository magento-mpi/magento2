<?php
/**
 * Tests for shipping method in shopping cart API.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Magento/Checkout/_files/quote_with_check_payment.php
 */
class Magento_Checkout_Model_Cart_Shipping_ApiTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        /** Collect rates before requesting them via API. */
        $this->_getQuoteFixture()->getShippingAddress()->setCollectShippingRates(true)->collectTotals()->save();
        parent::setUp();
    }

    /**
     * Test retrieving of shipping methods applicable to the shopping cart.
     *
     * @magentoConfigFixture current_store carriers/flatrate/active 1
     */
    public function testGetShippingMethodsList()
    {
        /** Retrieve the list of available shipping methods via API. */
        $shippingMethodsList = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartShippingList',
            array(
                $this->_getQuoteFixture()->getId()
            )
        );

        /** Verify the API call results. */
        $this->assertCount(1, $shippingMethodsList, 'There is exactly 1 shipping method expected.');
        $expectedItemData = array(
            'code' => 'flatrate_flatrate',
            'carrier' => 'flatrate',
            'carrier_title' => 'Flat Rate',
            'method' => 'flatrate',
            'method_title' => 'Fixed',
            'method_description' => null,
            'price' => 10
        );
        Magento_Test_Helper_Api::checkEntityFields($this, $expectedItemData, reset($shippingMethodsList));
    }

    /**
     * Test assigning shipping method to quote.
     *
     * @magentoConfigFixture current_store carriers/flatrate/active 1
     * @magentoDbIsolation enabled
     */
    public function testSetShippingMethod()
    {
        /** Prepare data. */
        $this->_getQuoteFixture()->getShippingAddress()->setShippingMethod(null)->save();
        /** @var Magento_Sales_Model_Quote $quoteBefore */
        $quoteBefore = Mage::getModel('Magento_Sales_Model_Quote')->load($this->_getQuoteFixture()->getId());
        $this->assertNull(
            $quoteBefore->getShippingAddress()->getShippingMethod(),
            "There should be no shipping method assigned to quote before assigning via API."
        );

        /** Retrieve the list of available shipping methods via API. */
        $shippingMethod = 'flatrate_flatrate';
        $isAdded = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartShippingMethod',
            array(
                $this->_getQuoteFixture()->getId(),
                $shippingMethod
            )
        );
        $this->assertTrue($isAdded, "Shipping method was not assigned to the quote.");

        /** Ensure that data was saved to DB. */
        /** @var Magento_Sales_Model_Quote $quoteAfter */
        $quoteAfter = Mage::getModel('Magento_Sales_Model_Quote')->load($this->_getQuoteFixture()->getId());
        $this->assertEquals(
            $shippingMethod,
            $quoteAfter->getShippingAddress()->getShippingMethod(),
            "Shipping method was assigned to quote incorrectly."
        );
    }

    /**
     * Retrieve the quote object created in fixture.
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _getQuoteFixture()
    {
        /** @var Magento_Sales_Model_Resource_Quote_Collection $quoteCollection */
        $quoteCollection = Mage::getModel('Magento_Sales_Model_Resource_Quote_Collection');
        /** @var Magento_Sales_Model_Quote $quote */
        $quote = $quoteCollection->getFirstItem();
        return $quote;
    }
}
