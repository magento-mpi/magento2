<?php
/**
 * Abstract checkout API testcase.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Mage/Checkout/_files/quote_with_simple_product.php
 */
abstract class Mage_Checkout_Model_Cart_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve quote created in fixture.
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getModel('Mage_Checkout_Model_Session');
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $session->getQuote();
        return $quote;
    }
}
