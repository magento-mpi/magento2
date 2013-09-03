<?php
/**
 * Abstract checkout API testcase.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Magento/Checkout/_files/quote_with_simple_product.php
 */
abstract class Magento_Checkout_Model_Cart_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve quote created in fixture.
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        /** @var Magento_Checkout_Model_Session $session */
        $session = Mage::getModel('Magento_Checkout_Model_Session');
        /** @var Magento_Sales_Model_Quote $quote */
        $quote = $session->getQuote();
        return $quote;
    }
}
