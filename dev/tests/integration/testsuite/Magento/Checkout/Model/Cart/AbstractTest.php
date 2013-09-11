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
     * @return \Magento\Sales\Model\Quote
     */
    protected function _getQuote()
    {
        /** @var \Magento\Checkout\Model\Session $session */
        $session = Mage::getModel('\Magento\Checkout\Model\Session');
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $session->getQuote();
        return $quote;
    }
}
